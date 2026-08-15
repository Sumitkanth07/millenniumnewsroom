<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $groupedBlogs;
    public ?NewsletterSubscriber $subscriber;

    public function __construct(Collection $groupedBlogs, ?NewsletterSubscriber $subscriber = null)
    {
        $this->groupedBlogs = $groupedBlogs;
        $this->subscriber = $subscriber;
    }

    public function envelope(): Envelope
    {
        $fromName = \App\Models\NewsletterSetting::getValue('from_name', config('mail.from.name', 'Millennium Newsroom'));
        $fromAddress = \App\Models\NewsletterSetting::getValue('from_email', config('mail.from.address', 'info@millenniumnewsroom.com'));
        $replyTo = \App\Models\NewsletterSetting::getValue('reply_to', 'info@millenniumnewsroom.com');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: [new Address($replyTo, $fromName)],
            subject: 'Weekly Digest: Top Stories & Executive Briefing | Millennium Newsroom',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.weekly-digest',
            with: [
                'groupedBlogs' => $this->groupedBlogs,
                'subscriber' => $this->subscriber,
            ]
        );
    }

    public function headers(): Headers
    {
        $text = [
            'Precedence' => 'bulk',
            'X-Auto-Response-Suppress' => 'OOF, AutoReply',
        ];

        if ($this->subscriber && $this->subscriber->unsubscribe_token) {
            $text['List-Unsubscribe'] = '<' . route('newsletter.unsubscribe', $this->subscriber->unsubscribe_token) . '>';
            $text['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
        }

        return new Headers(text: $text);
    }
}
