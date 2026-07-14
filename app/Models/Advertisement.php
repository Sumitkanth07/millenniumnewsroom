<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'name',
        'placement',
        'device',
        'type',
        'priority',
        'code',
        'code_desktop',
        'code_tablet',
        'code_mobile',
        'image',
        'image_tablet',
        'image_mobile',
        'target_url',
        'start_date',
        'end_date',
        'max_views',
        'max_clicks',
        'current_views',
        'current_clicks',
        'last_viewed_at',
        'last_clicked_at',
        'width',
        'height',
        'target_pages',
        'is_responsive',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_responsive' => 'boolean',
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'last_viewed_at' => 'datetime',
            'last_clicked_at' => 'datetime',
            'target_pages' => 'array',
            'priority' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'max_views' => 'integer',
            'max_clicks' => 'integer',
            'current_views' => 'integer',
            'current_clicks' => 'integer',
        ];
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('portal.ads.all');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('portal.ads.all');
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValidForDevice(string $currentDevice): bool
    {
        if ($this->device === 'all') {
            return true;
        }
        return $this->device === $currentDevice;
    }

    public function isValidForPage(string $pageType): bool
    {
        if (empty($this->target_pages)) {
            return true;
        }
        return in_array($pageType, $this->target_pages, true);
    }

    public function isUnderLimits(): bool
    {
        if ($this->max_views !== null && $this->current_views >= $this->max_views) {
            return false;
        }
        if ($this->max_clicks !== null && $this->current_clicks >= $this->max_clicks) {
            return false;
        }
        return true;
    }

    public function isScheduled(): bool
    {
        $now = now();
        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }
        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }
        return true;
    }
}
