<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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

    public function seoSetting(): MorphOne
    {
        return $this->morphOne(SeoSetting::class, 'seoable');
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

    public function getThumbnailUrl(): string
    {
        $image = $this->featured_image ?: $this->image;
        if (!$image) {
            return asset('images/default.jpg'); // or fallback placeholder
        }

        // If it's a URL, return it directly
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        // If it starts with storage/ or uploads/
        $cleanImage = ltrim($image, '/');
        
        // Check if there is a thumbnail version (inside thumbs/ subdirectory of the parent folder)
        $pathInfo = pathinfo($cleanImage);
        $thumbPath = ($pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '') . 'thumbs/' . $pathInfo['filename'] . '.webp';
        
        if (file_exists(public_path($thumbPath))) {
            return asset($thumbPath);
        }
        
        // Fallback to WebP of main image if it exists
        $webpPath = ($pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '') . $pathInfo['filename'] . '.webp';
        if (file_exists(public_path($webpPath))) {
            return asset($webpPath);
        }

        return asset($cleanImage);
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

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function fetchWithFallback(int $neededCount = 4, array $excludeIds = [], ?int $categoryId = null)
    {
        $results = collect();

        // 1. Posts from same category
        if ($categoryId) {
            $catPosts = static::published()
                ->with(['category', 'author'])
                ->where('category_id', $categoryId)
                ->whereNotIn('id', $excludeIds)
                ->latest('published_at')
                ->take($neededCount)
                ->get();

            $results = $results->concat($catPosts);
            $excludeIds = array_merge($excludeIds, $results->pluck('id')->all());
        }

        // 2. Latest News
        if ($results->count() < $neededCount) {
            $latest = static::published()
                ->with(['category', 'author'])
                ->whereNotIn('id', $excludeIds)
                ->latest('published_at')
                ->take($neededCount - $results->count())
                ->get();

            $results = $results->concat($latest);
            $excludeIds = array_merge($excludeIds, $results->pluck('id')->all());
        }

        // 3. Trending Posts
        if ($results->count() < $neededCount) {
            $trending = static::published()
                ->with(['category', 'author'])
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('is_trending')
                ->orderByDesc('views_count')
                ->latest('published_at')
                ->take($neededCount - $results->count())
                ->get();

            $results = $results->concat($trending);
            $excludeIds = array_merge($excludeIds, $results->pluck('id')->all());
        }

        // 4. Most Viewed
        if ($results->count() < $neededCount) {
            $mostViewed = static::published()
                ->with(['category', 'author'])
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('views_count')
                ->take($neededCount - $results->count())
                ->get();

            $results = $results->concat($mostViewed);
            $excludeIds = array_merge($excludeIds, $results->pluck('id')->all());
        }

        // 5. Editor's Picks
        if ($results->count() < $neededCount) {
            $featured = static::published()
                ->with(['category', 'author'])
                ->whereNotIn('id', $excludeIds)
                ->where('is_featured', true)
                ->latest('published_at')
                ->take($neededCount - $results->count())
                ->get();

            $results = $results->concat($featured);
            $excludeIds = array_merge($excludeIds, $results->pluck('id')->all());
        }

        // 6. Recent Articles
        if ($results->count() < $neededCount) {
            $recent = static::published()
                ->with(['category', 'author'])
                ->whereNotIn('id', $excludeIds)
                ->latest('published_at')
                ->take($neededCount - $results->count())
                ->get();

            $results = $results->concat($recent);
        }

        return $results;
    }

    protected static function booted()
    {
        static::saved(function ($blog) {
            \Illuminate\Support\Facades\Cache::forget('frontend.home.payload');
            \Illuminate\Support\Facades\Cache::forget('footer.categories.partial');
        });

        static::deleted(function ($blog) {
            \Illuminate\Support\Facades\Cache::forget('frontend.home.payload');
            \Illuminate\Support\Facades\Cache::forget('footer.categories.partial');
        });
    }
}
