<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class FeedbackSubmission extends Mailable
{
    public function __construct(
        public string $userName,
        public string $userEmail,
        public string $type,
        public string $feedbackMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->type} no vietnes lietotāja",
            replyTo: [new Address($this->userEmail, $this->userName)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-submission',
            text: 'emails.feedback-submission-text',
        );
    }
}
