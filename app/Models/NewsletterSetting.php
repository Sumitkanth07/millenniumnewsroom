<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NewsletterSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("newsletter_setting.{$key}", 300, function () use ($key, $default) {
            $record = static::where('key', $key)->first();
            return $record ? $record->value : $default;
        });
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("newsletter_setting.{$key}");
    }

    public static function getAllSettings(): array
    {
        $defaults = [
            'enable_new_post_notifications' => '1',
            'enable_weekly_digest' => '1',
            'weekly_send_day' => '1', // 1 = Monday
            'weekly_send_time' => '05:00',
            'timezone' => 'Asia/Kolkata',
            'from_name' => 'MILLENNIUM NEWSROOM',
            'from_email' => 'newsletter@millenniumnewsroom.com',
            'reply_to' => 'editor@millenniumnewsroom.com',
            'batch_size' => '100',
            'batch_delay_seconds' => '0',
            'default_email_footer' => 'Millennium Newsroom • Business, Markets and Policy Journalism',
        ];

        $records = static::all()->pluck('value', 'key')->toArray();

        return array_merge($defaults, $records);
    }
}
