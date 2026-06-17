<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content',
        'featured_image',
        'image_alt',
        'gallery_images',
        'category_id',
        'subcategory_id',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'schema_type',
        'views',
        'reading_time',
        'featured',
        'breaking_news',
        'trending',
        'status',
        'published_at'
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'featured' => 'boolean',
            'breaking_news' => 'boolean',
            'trending' => 'boolean',
            'published_at' => 'datetime'
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function viewsLog()
    {
        return $this->hasMany(PostView::class);
    }

    public function socialPostLogs()
    {
        return $this->hasMany(SocialPostLog::class);
    }

    public function seoSetting()
    {
        return $this->morphOne(SeoSetting::class, 'seoable');
    }
}