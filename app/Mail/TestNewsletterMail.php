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
        $fromName = \App\Models\NewsletterSetting::getValue('from_name', config('mail.from.name', 'Millennium Newsroom'));
        $fromAddress = \App\Models\NewsletterSetting::getValue('from_email', config('mail.from.address', 'info@millenniumnewsroom.com'));
        $replyTo = \App\Models\NewsletterSetting::getValue('reply_to', 'info@millenniumnewsroom.com');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: [new Address($replyTo, $fromName)],
            subject: 'Test Broadcast: Newsletter System Verification | Millennium Newsroom',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.test-email',
        );
    }
}
