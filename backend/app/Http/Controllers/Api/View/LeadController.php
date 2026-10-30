<?php

namespace App\Http\Controllers\Api\View;

use App\Http\Controllers\Controller;
use App\Mail\NewLead;
use App\Models\Lead;
use App\Services\SmartCaptcha;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class LeadController extends Controller
{
    // Быстрее человек форму из 9 полей не заполнит
    private const MIN_FILL_MS = 3000;

    // Ссылки и HTML-теги в текстовых полях — признак спама
    private const NO_LINKS = 'not_regex:/(https?:\/\/|www\.|<[^>]*>)/iu';

    public function store(Request $request): JsonResponse
    {
        // Ловушка для ботов: скрытое поле заполнено или форма отправлена слишком быстро.
        // Отвечаем «успехом», чтобы бот не понял, что его отсеяли
        if ($request->filled('company_site')) {
            return $this->accepted();
        }

        $fillMs = (int) $request->input('form_time');
        if ($fillMs < self::MIN_FILL_MS) {
            Log::info('Lead dropped: too fast', ['ms' => $fillMs, 'ip' => $request->ip()]);

            return $this->accepted();
        }

        $captchaRequired = SmartCaptcha::enabled();

        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:255', self::NO_LINKS],
            'first_name' => ['required', 'string', 'max:255', self::NO_LINKS],
            'middle_name' => ['nullable', 'string', 'max:255', self::NO_LINKS],
            'birthday' => 'required|date|before:today|after:1900-01-01',
            'city' => ['required', 'string', 'max:255', self::NO_LINKS],
            'workplace' => ['required', 'string', 'max:255', self::NO_LINKS],
            'position' => ['required', 'string', 'max:255', self::NO_LINKS],
            'email' => 'required|email|max:255',
            'phone' => ['bail', 'required', 'string', 'max:32', 'regex:/^[\d\s()+\-]{6,}$/', function (string $attribute, string $value, Closure $fail) {
                $digits = strlen(preg_replace('/\D/', '', $value));
                if ($digits < 10 || $digits > 15) {
                    $fail('Укажите корректный номер телефона.');
                }
            }],
            'source' => ['nullable', 'string', 'max:255', self::NO_LINKS],
            'consent' => 'accepted',
            'captcha' => [$captchaRequired ? 'required' : 'nullable', 'string', 'max:4096'],
        ], [
            'last_name.required' => 'Укажите фамилию.',
            'first_name.required' => 'Укажите имя.',
            'birthday.required' => 'Укажите дату рождения.',
            'birthday.date' => 'Укажите корректную дату рождения.',
            'birthday.before' => 'Укажите корректную дату рождения.',
            'birthday.after' => 'Укажите корректную дату рождения.',
            'city.required' => 'Укажите город.',
            'workplace.required' => 'Укажите место работы.',
            'position.required' => 'Укажите должность.',
            'email.required' => 'Укажите e-mail.',
            'email.email' => 'Укажите корректный e-mail.',
            'phone.required' => 'Укажите телефон.',
            'phone.regex' => 'Укажите корректный номер телефона.',
            'consent.accepted' => 'Необходимо согласие на обработку персональных данных.',
            'captcha.required' => 'Подтвердите, что вы не робот.',
            '*.max' => 'Слишком длинное значение.',
            '*.not_regex' => 'Ссылки в этом поле недопустимы.',
        ]);

        if ($captchaRequired && ! SmartCaptcha::passes($validated['captcha'], $request->ip())) {
            throw ValidationException::withMessages([
                'captcha' => 'Проверка не пройдена. Подтвердите, что вы не робот, ещё раз.',
            ]);
        }

        // Ivan@Mail.ru и ivan@mail.ru — один контакт
        $validated = array_merge($validated, ['email' => mb_strtolower(trim($validated['email']))]);

        // Повторная заявка с теми же контактами за сутки — не сохраняем и не шлём письмо
        $duplicate = Lead::where('created_at', '>=', now()->subDay())
            ->where(fn ($q) => $q->where('email', $validated['email'])->orWhere('phone', $validated['phone']))
            ->exists();
        if ($duplicate) {
            Log::info('Lead dropped: duplicate', ['ip' => $request->ip()]);

            return $this->accepted();
        }

        $lead = Lead::create([
            ...Arr::except($validated, ['consent', 'captcha']),
            'ip' => $request->ip(),
            'consent_at' => now(),
        ]);

        // Письмо уходит через очередь; заявка уже сохранена,
        // поэтому сбой постановки в очередь не должен ломать ответ пользователю
        $recipient = config('services.leads.email');
        if ($recipient) {
            try {
                Mail::to($recipient)->queue(new NewLead($lead));
            } catch (Throwable $e) {
                report($e);
            }
        } else {
            Log::warning('services.leads.email не задан');
        }

        return $this->accepted();
    }

    private function accepted(): JsonResponse
    {
        return response()->json(['message' => 'Заявка отправлена'], 201);
    }
}
