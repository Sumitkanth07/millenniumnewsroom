<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Category extends Model
{
    public function seoSetting(): MorphOne
    {
        return $this->morphOne(SeoSetting::class, 'seoable');
    }
    protected $fillable = ['parent_id', 'name', 'slug', 'image', 'description', 'meta_title', 'meta_description', 'sort_order', 'order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public static function slugFrom(string $name): string
    {
        return Str::slug($name) ?: 'category';
    }

    protected static function booted()
    {
        static::saved(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('frontend.home.payload');
            \Illuminate\Support\Facades\Cache::forget('navigation.items');
            \Illuminate\Support\Facades\Cache::forget('footer.categories.partial');
        });

        static::deleted(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('frontend.home.payload');
            \Illuminate\Support\Facades\Cache::forget('navigation.items');
            \Illuminate\Support\Facades\Cache::forget('footer.categories.partial');
        });
    }
}
