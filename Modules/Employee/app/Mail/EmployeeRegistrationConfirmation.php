<?php

namespace Modules\Employee\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Employee\Models\Employee;

class EmployeeRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ACME - Registration Confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'employee::emails.registration-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
