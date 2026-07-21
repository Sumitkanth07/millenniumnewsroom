<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['label', 'url', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted()
    {
        static::saved(function ($item) {
            \Illuminate\Support\Facades\Cache::forget('navigation.items');
        });

        static::deleted(function ($item) {
            \Illuminate\Support\Facades\Cache::forget('navigation.items');
        });
    }
}
