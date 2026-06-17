<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'featured_image',
        'gallery_images',
        'tags_cache',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'is_published',
        'is_featured',
        'is_breaking',
        'is_trending',
        'status',
        'scheduled_at',
        'published_at',
        'views_count',
        'featured_image_alt',
        'featured_image_title',
        'featured_image_caption',
        'featured_image_description',
        'robots_meta',
        'reading_time',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'is_trending' => 'boolean',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding By Slug
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function viewsLog(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function publicUrl(): string
    {
        return route('blog.category.show', [
            'category' => $this->category?->slug ?: 'news',
            'blog' => $this->slug,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Unique Slug
    |--------------------------------------------------------------------------
    */

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);

        $base = $slug ?: 'post';

        $count = 2;

        while (
            static::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($q) => $q->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {

            $slug = "{$base}-{$count}";

            $count++;
        }

        return $slug;
    }
}
