<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

// ShouldQueue: письмо отправляет воркер очереди (сервис queue в docker-compose),
// а не запрос пользователя
class NewLead extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->lead->email, $this->lead->full_name)],
            subject: 'Новая заявка «Хочу в календарь»: '.Str::limit($this->lead->full_name, 80),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-lead');
    }

    public function failed(Throwable $e): void
    {
        Log::error('NewLead: письмо не отправлено', ['lead_id' => $this->lead->id, 'error' => $e->getMessage()]);
    }
}
