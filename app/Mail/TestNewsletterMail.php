<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function envelope(): Envelope
    {
        $fromName = config('mail.from.name', 'MILLENNIUM NEWSROOM');
        $fromAddress = config('mail.from.address', 'newsletter@millenniumnewsroom.com');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: 'Test Broadcast: Newsletter System Verification | MILLENNIUM NEWSROOM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.test-email',
        );
    }
}
