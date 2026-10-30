<?php

namespace Tests\Feature;

use App\Mail\NewLead;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    private const VALIDATE_URL = 'https://smartcaptcha.yandexcloud.net/validate';

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Http::preventStrayRequests();
        config([
            'services.leads.email' => 'leads@example.ru',
            'services.smartcaptcha.server_key' => null,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return [
            'last_name' => 'Иванов',
            'first_name' => 'Иван',
            'birthday' => '1980-05-20',
            'city' => 'Кемерово',
            'workplace' => 'ООО Ромашка',
            'position' => 'Директор',
            'email' => 'ivan@mail.ru',
            'phone' => '+7 999 123-45-67',
            'source' => '/participation/gold',
            'consent' => true,
            'form_time' => 5000,
            ...$overrides,
        ];
    }

    private function postLead(array $overrides = [])
    {
        return $this->postJson('/api/leads', $this->payload($overrides));
    }

    public function test_valid_lead_is_saved_and_mail_is_queued(): void
    {
        $this->postLead()->assertCreated();

        $this->assertDatabaseHas('leads', ['email' => 'ivan@mail.ru', 'source' => '/participation/gold']);
        Mail::assertQueued(NewLead::class, fn (NewLead $mail) => $mail->hasTo('leads@example.ru'));
    }

    public function test_missing_recipient_is_logged_and_lead_is_saved(): void
    {
        config(['services.leads.email' => null]);
        Log::spy();

        $this->postLead()->assertCreated();

        $this->assertDatabaseCount('leads', 1);
        Mail::assertNothingQueued();
        Log::shouldHaveReceived('warning')->once()->with('services.leads.email не задан');
    }

    public function test_honeypot_is_dropped_silently(): void
    {
        Log::spy();

        $this->postLead(['company_site' => 'https://spam.example'])->assertCreated();

        $this->assertDatabaseCount('leads', 0);
        Mail::assertNothingQueued();
        Log::shouldNotHaveReceived('info');
    }

    public function test_too_fast_submit_is_dropped_and_logged(): void
    {
        Log::spy();

        $this->postLead(['form_time' => 1200])->assertCreated();

        $this->assertDatabaseCount('leads', 0);
        Log::shouldHaveReceived('info')->once()
            ->with('Lead dropped: too fast', \Mockery::on(fn (array $context) => $context['ms'] === 1200));
    }

    public function test_duplicate_is_dropped_and_logged_regardless_of_email_case(): void
    {
        $this->postLead(['email' => 'Ivan@Mail.ru'])->assertCreated();
        $this->assertDatabaseHas('leads', ['email' => 'ivan@mail.ru']);

        Log::spy();
        $this->postLead(['email' => ' IVAN@mail.ru ', 'phone' => '+7 999 000-00-00'])->assertCreated();

        $this->assertDatabaseCount('leads', 1);
        Mail::assertQueuedCount(1);
        Log::shouldHaveReceived('info')->once()->with('Lead dropped: duplicate', \Mockery::type('array'));
    }

    public function test_phone_requires_10_to_15_digits(): void
    {
        foreach (['123-45-67', '+7 (999) 123-45-67-12345678'] as $phone) {
            $this->postLead(['phone' => $phone])
                ->assertUnprocessable()
                ->assertJsonPath('errors.phone.0', 'Укажите корректный номер телефона.');
        }

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_source_with_link_is_rejected(): void
    {
        $this->postLead(['source' => 'https://spam.example'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('source');

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_captcha_is_required_when_server_key_is_set(): void
    {
        config(['services.smartcaptcha.server_key' => 'test-key']);

        $this->postLead()->assertUnprocessable()->assertJsonValidationErrors('captcha');

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_captcha_ok_saves_lead(): void
    {
        config(['services.smartcaptcha.server_key' => 'test-key']);
        Http::fake([self::VALIDATE_URL => Http::response(['status' => 'ok'])]);

        $this->postLead(['captcha' => 'token'])->assertCreated();

        $this->assertDatabaseCount('leads', 1);
    }

    public function test_captcha_failed_rejects_lead(): void
    {
        config(['services.smartcaptcha.server_key' => 'test-key']);
        Http::fake([self::VALIDATE_URL => Http::response(['status' => 'failed'])]);

        $this->postLead(['captcha' => 'token'])->assertUnprocessable()->assertJsonValidationErrors('captcha');

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_captcha_server_error_fails_open(): void
    {
        config(['services.smartcaptcha.server_key' => 'test-key']);
        Http::fake([self::VALIDATE_URL => Http::response('', 500)]);
        Log::spy();

        $this->postLead(['captcha' => 'token'])->assertCreated();

        $this->assertDatabaseCount('leads', 1);
        Log::shouldNotHaveReceived('critical');
    }

    public function test_captcha_client_error_fails_open_and_logs_critical(): void
    {
        config(['services.smartcaptcha.server_key' => 'wrong-key']);
        Http::fake([self::VALIDATE_URL => Http::response(['status' => 'failed', 'message' => 'Authentication failed'], 403)]);
        Log::spy();

        $this->postLead(['captcha' => 'token'])->assertCreated();

        $this->assertDatabaseCount('leads', 1);
        Log::shouldHaveReceived('critical')->once()
            ->with('SmartCaptcha отвергла запрос, капча не работает', \Mockery::on(fn (array $context) => $context['status'] === 403));
    }

    public function test_mail_template_renders_with_configured_timezone(): void
    {
        config(['services.leads.timezone' => 'Asia/Novokuznetsk']);
        $lead = Lead::create([...$this->payload(), 'consent_at' => now()]);

        $html = (new NewLead($lead))->render();

        $this->assertStringContainsString('name="viewport"', $html);
        $this->assertStringContainsString($lead->created_at->timezone('Asia/Novokuznetsk')->format('d.m.Y H:i'), $html);
    }

    public function test_mail_links_are_normalized_and_text_is_kept(): void
    {
        $lead = Lead::create([...$this->payload(), 'consent_at' => now()]);

        $html = (new NewLead($lead))->render();

        $this->assertStringContainsString('href="tel:+79991234567">+7 999 123-45-67</a>', $html);
        $this->assertStringContainsString('href="mailto:ivan@mail.ru">ivan@mail.ru</a>', $html);
    }

    public function test_mailto_encodes_dangerous_characters_and_keeps_at_sign(): void
    {
        $lead = Lead::create([...$this->payload(['email' => 'a%b?c#d@mail.ru']), 'consent_at' => now()]);

        $html = (new NewLead($lead))->render();

        $this->assertStringContainsString('href="mailto:a%25b%3Fc%23d@mail.ru">a%b?c#d@mail.ru</a>', $html);
    }

    public function test_mail_has_reply_to_and_limited_subject(): void
    {
        $lead = Lead::create([...$this->payload(['last_name' => str_repeat('я', 100)]), 'consent_at' => now()]);

        $mail = new NewLead($lead);

        $mail->assertHasReplyTo('ivan@mail.ru', $lead->full_name);
        $mail->assertHasSubject('Новая заявка «Хочу в календарь»: '.str_repeat('я', 80).'...');
    }

    public function test_mail_failure_is_logged(): void
    {
        $lead = Lead::create([...$this->payload(), 'consent_at' => now()]);
        Log::spy();

        (new NewLead($lead))->failed(new \RuntimeException('smtp down'));

        Log::shouldHaveReceived('error')->once()
            ->with('NewLead: письмо не отправлено', ['lead_id' => $lead->id, 'error' => 'smtp down']);
    }

    public function test_full_name_accessor_and_hidden_fields(): void
    {
        $lead = Lead::create([...$this->payload(['middle_name' => 'Иванович']), 'ip' => '127.0.0.1', 'consent_at' => now()]);

        $this->assertSame('Иванов Иван Иванович', $lead->full_name);
        $this->assertSame([], array_intersect(['ip', 'birthday', 'phone', 'email'], array_keys($lead->toArray())));
    }
}
