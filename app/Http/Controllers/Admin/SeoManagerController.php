<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Tag;
use App\Models\Author;
use App\Models\SeoSetting;
use App\Models\Redirect;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoManagerController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'inventory');
        $data = ['tab' => $tab];

        // 1. URL Inventory
        if ($tab === 'inventory') {
            $data['inventory'] = $this->getInventory();
        }

        // 2. SEO Audit
        if ($tab === 'audit') {
            $auditData = $this->getAudit();
            $data['audit'] = $auditData['issues'];
            $data['seoScore'] = $auditData['score'];
        }

        // 3. Sitemap
        if ($tab === 'sitemap') {
            $data['sitemapUrls'] = $this->getSitemapUrls();
        }

        // 4. Robots & AI
        if ($tab === 'robots') {
            $data['robots_txt'] = Setting::getValue('robots_txt', "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login");
            $data['llms_txt'] = Setting::getValue('llms_txt', "# LLM Crawler Directives\nUser-agent: *\nDisallow: /admin/\n");
        }

        // 5. Redirects
        if ($tab === 'redirects') {
            $data['redirects'] = Redirect::latest()->get();
        }

        // 6. Monitoring & 404s
        if ($tab === 'monitoring') {
            $data['logs'] = Cache::get('seo_404_logs', []);
            $data['brokenLinks'] = $this->checkBrokenLinks();
            
            // Map recommendations for 404 logs
            $data['logs'] = collect($data['logs'])->map(function ($log) {
                $log['recommendation'] = $this->getRedirectRecommendation($log['path']);
                return $log;
            })->toArray();
        }

        return view('admin.seo.index', $data);
    }

    public function edit(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');
        $path = $request->query('path');

        $seo = null;
        $title = '';
        $url = '';

        if ($type === 'Custom' || $type === 'Homepage') {
            $seo = SeoSetting::firstOrCreate([
                'seoable_type' => 'Path:' . $path,
                'seoable_id' => 0
            ]);
            $title = $type . ': ' . $path;
            $url = url($path);
        } else {
            $modelClass = 'App\\Models\\' . $type;
            if (class_exists($modelClass)) {
                $model = $modelClass::findOrFail($id);
                $seo = SeoSetting::firstOrCreate([
                    'seoable_type' => $modelClass,
                    'seoable_id' => $id
                ]);
                $title = $type . ': ' . ($model->title ?? $model->name ?? $model->slug);
                
                // Get URL
                if (method_exists($model, 'publicUrl')) {
                    $url = $model->publicUrl();
                } elseif ($type === 'Category') {
                    $url = route('category.show', $model->slug);
                } elseif ($type === 'Page') {
                    $url = route('page.show', $model->slug);
                } elseif ($type === 'Author') {
                    $url = route('author.show', $model->slug);
                } else {
                    $url = url('/' . $model->slug);
                }
            } else {
                abort(404, 'Invalid SEO Target type');
            }
        }

        return view('admin.seo.edit', compact('seo', 'title', 'url', 'type', 'id', 'path'));
    }

    public function update(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $path = $request->input('path');

        $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots_meta' => ['nullable', 'string', 'max:120'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'image', 'max:1024'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string'],
            'twitter_image' => ['nullable', 'image', 'max:1024'],
            'schema_type' => ['required', 'string', 'max:120'],
            'custom_schema' => ['nullable', 'string'],
        ]);

        if ($type === 'Custom' || $type === 'Homepage') {
            $seo = SeoSetting::where('seoable_type', 'Path:' . $path)
                ->where('seoable_id', 0)
                ->firstOrFail();
        } else {
            $modelClass = 'App\\Models\\' . $type;
            $seo = SeoSetting::where('seoable_type', $modelClass)
                ->where('seoable_id', $id)
                ->firstOrFail();
        }

        $data = $request->only([
            'meta_title', 'meta_description', 'meta_keywords', 
            'canonical_url', 'robots_meta', 'og_title', 'og_description', 'schema_type'
        ]);

        // Handlers for social image uploads
        if ($request->hasFile('og_image')) {
            $file = $request->file('og_image');
            $baseName = 'og_' . time() . '_' . uniqid();
            $optimized = \App\Helpers\ImageOptimizer::optimize($file, public_path('uploads/seo'), $baseName);
            $data['og_image'] = 'uploads/seo/' . $optimized['main'];
        }

        // Twitter Card fields inside schema_data JSON
        $schemaData = $seo->schema_data ?: [];
        $schemaData['twitter_title'] = $request->input('twitter_title');
        $schemaData['twitter_description'] = $request->input('twitter_description');
        $schemaData['custom_schema'] = $request->input('custom_schema');

        if ($request->hasFile('twitter_image')) {
            $file = $request->file('twitter_image');
            $baseName = 'tw_' . time() . '_' . uniqid();
            $optimized = \App\Helpers\ImageOptimizer::optimize($file, public_path('uploads/seo'), $baseName);
            $schemaData['twitter_image'] = 'uploads/seo/' . $optimized['main'];
        }

        $data['schema_data'] = $schemaData;

        $seo->update($data);

        // Sync with parent model for single source of truth
        if ($type !== 'Custom' && $type !== 'Homepage') {
            $modelClass = 'App\\Models\\' . $type;
            if (class_exists($modelClass)) {
                $model = $modelClass::find($id);
                if ($model) {
                    $updateData = [];
                    $table = $model->getTable();
                    $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);
                    foreach (['meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots_meta'] as $col) {
                        if (in_array($col, $columns)) {
                            $updateData[$col] = $request->input($col);
                        }
                    }
                    if (!empty($updateData)) {
                        $model->update($updateData);
                    }
                }
            }
        }

        // Clear payload caches
        Cache::forget('frontend.home.payload');

        return redirect()->route('admin.seo.index')->with('status', 'SEO Settings updated successfully.');
    }

    public function updateRobots(Request $request)
    {
        $request->validate([
            'robots_txt' => ['nullable', 'string'],
            'llms_txt' => ['nullable', 'string'],
        ]);

        Setting::setValue('robots_txt', $request->input('robots_txt', ''));
        Setting::setValue('llms_txt', $request->input('llms_txt', ''));

        return back()->with('status', 'Robots.txt and LLMs.txt directives saved.');
    }

    public function clearCache()
    {
        Cache::forget('frontend.home.payload');
        Cache::forget('sitemap_data');
        return back()->with('status', 'Sitemap and homepage caches successfully cleared.');
    }
    private function getInventory(): array
    {
        $inventory = [];

        // 1. Homepage
        $inventory[] = [
            'title' => 'MILLENNIUM NEWSROOM Homepage',
            'path' => '/',
            'url' => url('/'),
            'type' => 'Homepage',
            'id' => 0,
            'seo' => SeoSetting::where('seoable_type', 'Path:/')->where('seoable_id', 0)->first(),
            'model' => null
        ];

        // 2. Static Pages
        foreach (Page::with('seoSetting')->orderBy('title')->get() as $page) {
            $path = '/page/' . $page->slug;
            $inventory[] = [
                'title' => $page->title,
                'path' => $path,
                'url' => route('page.show', $page->slug),
                'type' => 'Page',
                'id' => $page->id,
                'seo' => $page->seoSetting,
                'model' => $page
            ];
        }

        // 3. Categories
        foreach (Category::with('seoSetting')->orderBy('name')->get() as $category) {
            $path = '/category/' . $category->slug;
            $inventory[] = [
                'title' => $category->name,
                'path' => $path,
                'url' => route('category.show', $category->slug),
                'type' => 'Category',
                'id' => $category->id,
                'seo' => $category->seoSetting,
                'model' => $category
            ];
        }

        // 4. Posts
        foreach (Blog::with(['category', 'seoSetting'])->latest('published_at')->get() as $blog) {
            $path = '/' . ($blog->category?->slug ?: 'news') . '/' . $blog->slug;
            $inventory[] = [
                'title' => $blog->title,
                'path' => $path,
                'url' => $blog->publicUrl(),
                'type' => 'Blog',
                'id' => $blog->id,
                'seo' => $blog->seoSetting,
                'model' => $blog
            ];
        }

        // 5. Authors
        foreach (Author::with('seoSetting')->orderBy('name')->get() as $author) {
            $path = '/author/' . $author->slug;
            $inventory[] = [
                'title' => $author->name . ' (Author Profile)',
                'path' => $path,
                'url' => route('author.show', $author->slug),
                'type' => 'Author',
                'id' => $author->id,
                'seo' => $author->seoSetting,
                'model' => $author
            ];
        }

        // 6. Savings Calculator
        $inventory[] = [
            'title' => 'Savings Calculator Tool',
            'path' => '/savings-calculator',
            'url' => route('calculator.show'),
            'type' => 'Custom',
            'id' => 0,
            'seo' => SeoSetting::where('seoable_type', 'Path:/savings-calculator')->where('seoable_id', 0)->first(),
            'model' => null
        ];

        return $inventory;
    }

    private function getAudit(): array
    {
        $issues = [];
        $inventory = $this->getInventory();
        $totalChecks = 0;
        $totalFailures = 0;

        foreach ($inventory as $item) {
            $seo = $item['seo'];
            $model = $item['model'];
            $metaTitle = $seo?->meta_title ?: ($model?->meta_title ?? null);
            $metaDescription = $seo?->meta_description ?: ($model?->meta_description ?? null);
            $canonicalUrl = $seo?->canonical_url;
            $schemaType = $seo?->schema_type;
            $ogImage = $seo?->og_image ?: ($item['type'] === 'Blog' && $model ? ($model->featured_image ?: $model->image) : null);

            $itemIssues = [];
            $totalChecks += 5;

            if (empty($metaTitle)) {
                $itemIssues[] = 'Missing Meta Title';
            }
            if (empty($metaDescription)) {
                $itemIssues[] = 'Missing Meta Description';
            }
            if (empty($canonicalUrl)) {
                $itemIssues[] = 'Missing Canonical URL';
            }
            if (empty($schemaType) || $schemaType === 'None') {
                $itemIssues[] = 'Missing Schema Markup';
            }
            if (empty($ogImage)) {
                $itemIssues[] = 'Missing Open Graph Image';
            }

            // Check missing Alt tag for blogs
            if ($item['type'] === 'Blog' && $model) {
                if ($model->featured_image || $model->image) {
                    $totalChecks += 1;
                    if (empty($model->featured_image_alt)) {
                        $itemIssues[] = 'Missing Image Alt Tag';
                    }
                }
            }

            $totalFailures += count($itemIssues);

            if (!empty($itemIssues)) {
                $issues[] = [
                    'title' => $item['title'],
                    'path' => $item['path'],
                    'type' => $item['type'],
                    'id' => $item['id'],
                    'issues' => $itemIssues
                ];
            }
        }

        $score = $totalChecks > 0 ? (int)round((1 - $totalFailures / $totalChecks) * 100) : 100;
        $score = max(0, min(100, $score));

        return [
            'issues' => $issues,
            'score' => $score
        ];
    }

    private function checkBrokenLinks(): array
    {
        $broken = [];
        $inventory = $this->getInventory();
        $urlsToCheck = [];

        foreach ($inventory as $item) {
            if ($item['type'] === 'Homepage' || $item['type'] === 'Page' || $item['type'] === 'Category' || $item['type'] === 'Custom') {
                $urlsToCheck[] = $item;
            } elseif ($item['type'] === 'Blog') {
                if (count($urlsToCheck) < 25) {
                    $urlsToCheck[] = $item;
                }
            }
        }

        $httpKernel = app(\Illuminate\Contracts\Http\Kernel::class);

        foreach ($urlsToCheck as $item) {
            $request = Request::create($item['path']);
            try {
                $response = $httpKernel->handle($request);
                $status = $response->getStatusCode();
                if ($status === 404 || $status >= 500) {
                    $recommendation = $this->getRedirectRecommendation($item['path']);
                    $broken[] = [
                        'title' => $item['title'],
                        'path' => $item['path'],
                        'status' => $status,
                        'recommendation' => $recommendation
                    ];
                }
            } catch (\Throwable) {
                $broken[] = [
                    'title' => $item['title'],
                    'path' => $item['path'],
                    'status' => 500,
                    'recommendation' => '/'
                ];
            }
        }

        return $broken;
    }

    private function getRedirectRecommendation(string $path): string
    {
        $segments = explode('/', trim($path, '/'));
        $lastSegment = end($segments);

        if (empty($lastSegment)) {
            return '/';
        }

        $matchedBlog = Blog::where('slug', 'like', '%' . $lastSegment . '%')->first();
        if ($matchedBlog) {
            return $matchedBlog->publicUrl();
        }

        $matchedPage = Page::where('slug', 'like', '%' . $lastSegment . '%')->first();
        if ($matchedPage) {
            return route('page.show', $matchedPage->slug);
        }

        $matchedCategory = Category::where('slug', 'like', '%' . $lastSegment . '%')->first();
        if ($matchedCategory) {
            return route('category.show', $matchedCategory->slug);
        }

        return '/';
    }

    private function getSitemapUrls(): array
    {
        $urls = [];
        $appUrl = rtrim((string) config('app.url'), '/');

        $urls[] = $appUrl . '/';
        $urls[] = $appUrl . '/blog';
        $urls[] = $appUrl . '/savings-calculator';

        foreach (Page::where('is_published', true)->get(['slug']) as $page) {
            $urls[] = $appUrl . '/page/' . $page->slug;
        }

        foreach (Category::where('is_active', true)->get(['slug']) as $category) {
            $urls[] = $appUrl . '/category/' . $category->slug;
        }

        foreach (Blog::with('category')->where('is_published', true)->latest('published_at')->get() as $blog) {
            $urls[] = $appUrl . '/' . ($blog->category?->slug ?: 'news') . '/' . $blog->slug;
        }

        return $urls;
    }
}
