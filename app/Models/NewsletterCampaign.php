<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterCampaign extends Model
{
    protected $fillable = [
        'title',
        'campaign_type',
        'campaign_key',
        'post_id',
        'status',
        'total_subscribers',
        'sent_count',
        'failed_count',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_subscribers' => 'integer',
            'sent_count' => 'integer',
            'failed_count' => 'integer',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'post_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterEmailLog::class, 'campaign_id');
    }
}
