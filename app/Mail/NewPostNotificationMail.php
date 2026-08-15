<?php

namespace App\Mail;

use App\Helpers\NewsletterHelper;
use App\Models\Blog;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewPostNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Blog $blog;
    public ?NewsletterSubscriber $subscriber;
    public string $articleUrl;
    public string $imageUrl;

    public function __construct(Blog $blog, ?NewsletterSubscriber $subscriber = null)
    {
        $this->blog = $blog;
        $this->subscriber = $subscriber;
        $this->articleUrl = NewsletterHelper::getAbsoluteArticleUrl($blog);
        $this->imageUrl = NewsletterHelper::getAbsoluteImageUrl($blog->featured_image ?: $blog->image);
    }

    public function envelope(): Envelope
    {
        $fromName = config('mail.from.name', 'MILLENNIUM NEWSROOM');
        $fromAddress = config('mail.from.address', 'newsletter@millenniumnewsroom.com');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $this->blog->title . ' | MILLENNIUM NEWSROOM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.new-post',
            with: [
                'blog' => $this->blog,
                'subscriber' => $this->subscriber,
                'articleUrl' => $this->articleUrl,
                'imageUrl' => $this->imageUrl,
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
