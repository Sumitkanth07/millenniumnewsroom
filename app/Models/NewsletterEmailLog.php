<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterEmailLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'post_id',
        'campaign_type',
        'status',
        'sent_at',
        'error_message',
        'message_id',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class, 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscriber::class, 'subscriber_id');
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'post_id');
    }
}
