<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'company_name',
        'email',
        'phone',
            'address',
            'copyright_text',
            'footer_menus',
            'category_links',
            'social_links',
            'sitemap_links',
        ];

    protected function casts(): array
    {
        return [
            'footer_menus' => 'array',
            'category_links' => 'array',
            'social_links' => 'array',
            'sitemap_links' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'company_name' => 'MILLENNIUM NEWSROOM',
            'email' => 'info@millenniumnewsroom.com',
            'phone' => '+91 9876543210',
            'address' => 'New Delhi, India',
            'copyright_text' => '(c) '.date('Y').' MILLENNIUM NEWSROOM. All rights reserved.',
        ]);
    }
}
