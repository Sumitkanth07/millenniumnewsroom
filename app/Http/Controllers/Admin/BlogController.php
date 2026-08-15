<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewPostNotificationJob;
use App\Models\Author;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'author']);

        $sort = $request->query('sort');
        if ($sort === 'views_asc') {
            $query->orderBy('views_count', 'asc');
        } elseif ($sort === 'views_desc') {
            $query->orderBy('views_count', 'desc');
        } else {
            $query->latest();
        }

        return view('admin.blogs.index', [
            'blogs' => $query->paginate(12)->withQueryString(),
            'currentSort' => $sort,
        ]);
    }

    public function resetViews(Blog $blog)
    {
        $blog->newQuery()->where('id', $blog->id)->update(['views_count' => 0]);
        $blog->viewsLog()->delete();
        Cache::forget('admin.dashboard.payload');

        return back()->with('status', 'View count reset successfully.');
    }

    public function create()
    {
        return $this->formView(new Blog());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $data['category_id'] = $this->resolveCategory($request, $data['category_id'] ?? null);
        $data['slug'] = Blog::uniqueSlug($data['slug'] ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published') || $data['status'] === 'published';
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['status'] = $data['is_published'] ? 'published' : ($data['status'] ?? 'draft');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_breaking'] = $request->boolean('is_breaking');
        $data['is_trending'] = $request->boolean('is_trending');
        $data['reading_time'] = $this->readingTime($data['content']);
        unset($data['new_category_name'], $data['new_category_parent_id'], $data['remove_gallery_images']);

        $this->storeImages($request, $data);

        $blog = Blog::create($data);
        $this->syncTags($blog, $request->input('tags'));
        $this->clearPublishingCaches();

        if ($blog->is_published) {
            SendNewPostNotificationJob::dispatch($blog);
        }

        return redirect()->route('admin.blogs.index')->with('status', 'Post created.');
    }

    public function edit(Blog $blog)
    {
        return $this->formView($blog->load('tags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $wasPublished = (bool) $blog->is_published;
        $data = $this->validated($request);
        $data['category_id'] = $this->resolveCategory($request, $data['category_id'] ?? null);
        $data['slug'] = Blog::uniqueSlug($data['slug'] ?: $data['title'], $blog->id);
        $data['is_published'] = $request->boolean('is_published') || $data['status'] === 'published';
        $data['published_at'] = $data['is_published'] ? ($blog->published_at ?: now()) : null;
        $data['status'] = $data['is_published'] ? 'published' : ($data['status'] ?? 'draft');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_breaking'] = $request->boolean('is_breaking');
        $data['is_trending'] = $request->boolean('is_trending');
        $data['reading_time'] = $this->readingTime($data['content']);
        unset($data['new_category_parent_id'], $data['remove_gallery_images']);

        $this->storeImages($request, $data, $blog);
        $blog->update($data);
        $this->syncTags($blog, $request->input('tags'));
        $this->clearPublishingCaches();

        if (!$wasPublished && $blog->is_published) {
            SendNewPostNotificationJob::dispatch($blog);
        }

        return redirect()->route('admin.blogs.index')->with('status', 'Post updated.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        $this->clearPublishingCaches();

        return back()->with('status', 'Post deleted.');
    }

    private function formView(Blog $blog)
    {
        return view('admin.blogs.form', [
            'blog' => $blog,
            'categories' => Category::with('parent')->orderBy('sort_order')->orderBy('name')->get(),
            'authors' => Author::where('is_active', true)->orderBy('name')->get(),
            'tagList' => $blog->exists ? $blog->tags->pluck('name')->implode(', ') : '',
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'author_id' => ['nullable', 'exists:authors,id'],
            'image' => ['nullable', 'image', 'max:1024'],
            'featured_image' => ['nullable', 'image', 'max:1024'],
            'gallery_images.*' => ['nullable', 'image', 'max:1024'],
            'remove_gallery_images.*' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots_meta' => ['nullable', 'string', 'max:120'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'featured_image_title' => ['nullable', 'string', 'max:255'],
            'featured_image_caption' => ['nullable', 'string', 'max:255'],
            'featured_image_description' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,published,scheduled'],
            'tags' => ['nullable', 'string'],
            'new_category_name' => ['nullable', 'string', 'max:120'],
            'new_category_parent_id' => ['nullable', 'exists:categories,id'],
        ]);
    }

    private function storeImages(Request $request, array &$data, ?Blog $blog = null): void
    {
        /*
    |--------------------------------------------------------------------------
    | Shared Hosting Friendly Upload System
    |--------------------------------------------------------------------------
    */

        $uploadRoot = $this->publicUploadRoot();

        // Create upload folders if missing
        if (!file_exists($uploadRoot . '/uploads')) {
            mkdir($uploadRoot . '/uploads', 0775, true);
        }

        if (!file_exists($uploadRoot . '/uploads/gallery')) {
            mkdir($uploadRoot . '/uploads/gallery', 0775, true);
        }

        /*
    |--------------------------------------------------------------------------
    | Normal Image Upload
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $baseName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $destination = $uploadRoot . '/uploads';
            $optimized = \App\Helpers\ImageOptimizer::optimize($file, $destination, $baseName);
            $data['image'] = 'uploads/' . $optimized['main'];
        } else {
            unset($data['image']);
        }

        /*
    |--------------------------------------------------------------------------
    | Featured Image Upload
    |--------------------------------------------------------------------------
    |
    */

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $baseName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $destination = $uploadRoot . '/uploads';
            $optimized = \App\Helpers\ImageOptimizer::optimize($file, $destination, $baseName);
            $data['featured_image'] = 'uploads/' . $optimized['main'];
        } else {
            unset($data['featured_image']);
        }

        /*
    |--------------------------------------------------------------------------
    | Gallery Images Upload
    |--------------------------------------------------------------------------
    */

        $galleryImages = $blog?->gallery_images ?: [];
        $removeImages = $request->input('remove_gallery_images', []);

        if (! empty($removeImages)) {
            foreach ($removeImages as $image) {
                $filePath = public_path(ltrim($image, '/'));
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $galleryImages = array_values(array_diff($galleryImages, $removeImages));
            $data['gallery_images'] = $galleryImages;
        }

        if ($request->hasFile('gallery_images')) {
            $galleryImages = $blog && empty($removeImages) ? ($blog->gallery_images ?: []) : $galleryImages;

            foreach ($request->file('gallery_images') as $file) {
                $baseName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $destination = $uploadRoot . '/uploads/gallery';
                $optimized = \App\Helpers\ImageOptimizer::optimize($file, $destination, $baseName);
                $galleryImages[] = 'uploads/gallery/' . $optimized['main'];
            }
            $data['gallery_images'] = $galleryImages;
        }
    }

    private function publicUploadRoot(): string
    {
        return public_path();
    }
    private function readingTime(string $content): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($content)) / 220));
    }

    private function resolveCategory(Request $request, ?int $categoryId): ?int
    {
        $name = trim((string) $request->input('new_category_name'));
        if ($name === '') {
            return $categoryId;
        }

        return Category::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'parent_id' => $request->input('new_category_parent_id'), 'is_active' => true]
        )->id;
    }

    private function syncTags(Blog $blog, ?string $tags): void
    {
        $names = collect(explode(',', (string) $tags))->map(fn($tag) => trim($tag))->filter()->unique();
        $ids = $names->map(fn($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id)->all();

        $blog->tags()->sync($ids);
        $blog->updateQuietly(['tags_cache' => $names->implode(', ')]);
    }

    private function clearPublishingCaches(): void
    {
        Cache::forget('admin.dashboard.payload');
        Cache::forget('frontend.home.payload');
    }
}
