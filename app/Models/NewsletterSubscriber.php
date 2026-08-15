<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'name',
        'email',
        'status',
        'notify_new_post',
        'notify_weekly_digest',
        'unsubscribe_token',
        'subscribed_at',
        'unsubscribed_at',
        'last_email_sent_at',
        'last_delivery_status',
    ];

    protected function casts(): array
    {
        return [
            'notify_new_post' => 'boolean',
            'notify_weekly_digest' => 'boolean',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'last_email_sent_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = Str::random(64);
            }
            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }
        });
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(NewsletterEmailLog::class, 'subscriber_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status && in_array($status, ['active', 'unsubscribed', 'bounced', 'inactive'])) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function getUnsubscribeUrlAttribute(): string
    {
        return route('newsletter.unsubscribe', $this->unsubscribe_token);
    }

    public function getPreferencesUrlAttribute(): string
    {
        return route('newsletter.preferences', $this->unsubscribe_token);
    }
}
