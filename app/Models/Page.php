<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'banner_image', 'content', 'meta_title', 'meta_description', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function seoSetting()
    {
        return $this->morphOne(SeoSetting::class, 'seoable');
    }

    public static function slugFrom(string $title): string
    {
        return Str::slug($title) ?: 'page';
    }
}
