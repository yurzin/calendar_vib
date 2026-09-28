<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// ShouldQueue: письмо отправляет воркер очереди (сервис queue в docker-compose),
// а не запрос пользователя
class NewLead extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая заявка «Хочу в календарь»: ' . $this->lead->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-lead');
    }
}
