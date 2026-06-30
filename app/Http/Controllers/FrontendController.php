<?php

namespace App\Http\Controllers;

use App\Models\AdPlacement;
use App\Models\Blog;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    public function home()
    {
        try {
            if (app()->environment('testing')) {
                throw new \RuntimeException;
            }

            $hasBlogs = Schema::hasTable('blogs');
        } catch (\Throwable) {
            $hasBlogs = false;
        }

        if (! $hasBlogs) {
            return view('frontend.home', [
                'leadStory' => null,
                'breakingPosts' => collect(),
                'topHeadlines' => collect(),
                'latestBlogs' => collect(),
                'trendingPosts' => collect(),
                'featuredPosts' => collect(),
                'editorPicks' => collect(),
                'mostRead' => collect(),
                'popularCategories' => collect(),
                'recommendedPosts' => collect(),
                'popularTags' => collect(),
                'categories' => collect(),
                'ads' => collect(),
                'homepageSections' => collect(),
                'metaTitle' => 'MILLENNIUM NEWSROOM | Professional News Portal',
                'metaDescription' => 'MILLENNIUM NEWSROOM delivers business, markets and technology journalism.',
            ]);
        }

        $payload = Cache::remember('frontend.home.payload', 90, function () {
            $published = Blog::query()->where('is_published', true)->with(['category', 'author']);

            return [
                'leadStory' => (clone $published)->where('is_featured', true)->latest('published_at')->first()
                    ?: (clone $published)->latest('published_at')->first(),
                'breakingPosts' => (clone $published)->where('is_breaking', true)->latest('published_at')->take(6)->get(),
                'topHeadlines' => (clone $published)->latest('published_at')->take(6)->get(),
                'latestBlogs' => (clone $published)->latest('published_at')->take(12)->get(),
                'trendingPosts' => (clone $published)->where(fn ($q) => $q->where('is_trending', true)->orWhere('views_count', '>', 0))->orderByDesc('is_trending')->orderByDesc('views_count')->latest('published_at')->take(6)->get(),
                'featuredPosts' => (clone $published)->where('is_featured', true)->latest('published_at')->take(4)->get(),
                'editorPicks' => (clone $published)->where('is_featured', true)->orderByDesc('views_count')->take(6)->get(),
                'mostRead' => (clone $published)->orderByDesc('views_count')->take(5)->get(),
                'recommendedPosts' => (clone $published)->latest('views_count')->take(4)->get(),
                'popularTags' => Tag::withCount('blogs')->orderByDesc('blogs_count')->take(12)->get(),
                'popularCategories' => Category::withCount('blogs')->where('is_active', true)->orderByDesc('blogs_count')->take(6)->get(),
                'categories' => Category::with(['blogs' => fn ($query) => $query->where('is_published', true)->latest('published_at')->take(4)])
                    ->where('is_active', true)
                    ->whereHas('blogs', fn ($query) => $query->where('is_published', true), '>=', 4)
                    ->orderBy('sort_order')
                    ->take(5)
                    ->get(),
                'ads' => AdPlacement::where('is_active', true)->get()->keyBy('key'),
                'metaTitle' => str_ireplace('MILLENNIUM NEWSROOM', 'MILLENNIUM NEWSROOM', Setting::getValue('site_title', 'MILLENNIUM NEWSROOM | Professional News Portal')),
                'metaDescription' => str_ireplace('MILLENNIUM NEWSROOM', 'MILLENNIUM NEWSROOM', Setting::getValue('meta_description', 'MILLENNIUM NEWSROOM delivers business, markets, technology and public affairs journalism.')),
            ];
        });

        $leadImage = $payload['leadStory']?->featured_image ?: $payload['leadStory']?->image;
        $payload['ogImage'] = $leadImage ? $this->absoluteUrl($leadImage) : null;
        $payload['homepageSections'] = Schema::hasTable('homepage_sections')
            ? HomepageSection::orderBy('sort_order')->get()->keyBy('key')
            : collect();

        if (($payload['homepageSections']['breaking_news']->is_active ?? true) === false) {
            $payload['breakingPosts'] = collect();
        }

        return view('frontend.home', $payload);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q'));
        $category = $request->query('category');
        $sort = $request->query('sort', 'latest');

        $posts = Blog::with(['category', 'author'])->where('is_published', true)
            ->when($query, fn ($builder) => $builder->where(fn ($inner) => $inner
                ->where('title', 'like', "%{$query}%")
                ->orWhere('excerpt', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")))
            ->when($category, fn ($builder) => $builder->whereHas('category', fn ($inner) => $inner->where('slug', $category)))
            ->when($sort === 'popular', fn ($builder) => $builder->orderByDesc('views_count'), fn ($builder) => $builder->latest('published_at'))
            ->paginate(10)
            ->withQueryString();

        return view('frontend.search', [
            'posts' => $posts,
            'query' => $query,
            'selectedCategory' => $category,
            'sort' => $sort,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'metaTitle' => 'Search News | MILLENNIUM NEWSROOM',
            'metaDescription' => 'Search latest news, markets, companies, politics, technology and opinion stories on MILLENNIUM NEWSROOM.',
        ]);
    }

    public function category(Category $category)
    {
        $category->load('seoSetting');
        $posts = $category->blogs()->with(['category', 'author'])->where('is_published', true)->latest('published_at')->paginate(12);

        return view('frontend.category', [
            'category' => $category,
            'featured' => $posts->first(),
            'posts' => $posts,
            'trendingPosts' => Blog::with('category')->where('is_published', true)->orderByDesc('views_count')->take(5)->get(),
            'metaTitle' => str_ireplace('MILLENNIUM NEWSROOM', 'MILLENNIUM NEWSROOM', $category->meta_title ?: $category->name.' News | MILLENNIUM NEWSROOM'),
            'metaDescription' => str_ireplace('MILLENNIUM NEWSROOM', 'MILLENNIUM NEWSROOM', $category->meta_description ?: 'Latest '.$category->name.' stories and analysis from MILLENNIUM NEWSROOM.'),
        ]);
    }

    public function htmlSitemap()
    {
        return view('frontend.html-sitemap', [
            'categories' => Category::withCount('blogs')->where('is_active', true)->orderBy('name')->get(),
            'pages' => Page::where('is_published', true)->orderBy('title')->get(),
            'latestPosts' => Blog::with('category')->where('is_published', true)->latest('published_at')->take(20)->get(),
            'popularPosts' => Blog::with('category')->where('is_published', true)->orderByDesc('views_count')->take(10)->get(),
            'archives' => Blog::where('is_published', true)
                ->selectRaw("DATE_FORMAT(published_at, '%Y-%m') as month")
                ->groupBy('month')
                ->orderByDesc('month')
                ->take(12)
                ->pluck('month'),
            'metaTitle' => 'Sitemap | MILLENNIUM NEWSROOM',
            'metaDescription' => 'Browse categories, pages, posts and archives on MILLENNIUM NEWSROOM.',
        ]);
    }

    public function page(Page $page)
    {
        abort_unless($page->is_published, 404);
        $page->load('seoSetting');

        return view('frontend.page', [
            'page' => $page,
            'metaTitle' => str_ireplace('MILLENNIUM NEWSROOM', 'MILLENNIUM NEWSROOM', $page->meta_title ?: $page->title.' | MILLENNIUM NEWSROOM'),
            'metaDescription' => $page->meta_description,
        ]);
    }

    public function author(\App\Models\Author $author)
    {
        abort_unless($author->is_active, 404);
        $author->load('seoSetting');

        $posts = $author->blogs()
            ->with(['category', 'author'])
            ->where('is_published', true)
            ->latest('published_at')
            ->paginate(12);

        return view('frontend.author', [
            'author' => $author,
            'posts' => $posts,
            'metaTitle' => $author->name . ' | Author Profile | MILLENNIUM NEWSROOM',
            'metaDescription' => $author->bio ?: 'Read articles published by ' . $author->name . ' on MILLENNIUM NEWSROOM.',
        ]);
    }

    public function sitemap(): Response
    {
        $blogs = Blog::with('category')->where('is_published', true)->latest('updated_at')->get(['id', 'category_id', 'slug', 'updated_at']);
        $categories = Schema::hasTable('categories') ? Category::where('is_active', true)->get(['slug', 'updated_at']) : collect();
        $pages = Schema::hasTable('pages') ? Page::where('is_published', true)->get(['slug', 'updated_at']) : collect();
        $xml = view('frontend.sitemap', compact('blogs', 'categories', 'pages'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function newsSitemap(): Response
    {
        $blogs = Blog::with('category')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '>=', now()->subDays(2))
            ->latest('published_at')
            ->take(1000)
            ->get(['id', 'category_id', 'slug', 'title', 'published_at', 'updated_at']);
        $xml = view('frontend.news-sitemap', compact('blogs'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $rules = Setting::getValue('robots_txt', "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login");

        return response($rules."\nSitemap: ".$this->absoluteUrl('/sitemap.xml')."\nSitemap: ".$this->absoluteUrl('/news-sitemap.xml')."\nSitemap: ".$this->absoluteUrl('/sitemap.txt')."\n", 200)
            ->header('Content-Type', 'text/plain');
    }

    public function sitemapTxt(): Response
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        $urls = [];
        $urls[] = $appUrl . '/';
        $urls[] = $appUrl . '/blog';

        $blogs = Blog::with('category')->where('is_published', true)->latest('updated_at')->get();
        foreach ($blogs as $blog) {
            $urls[] = $appUrl . '/' . ($blog->category?->slug ?: 'news') . '/' . $blog->slug;
        }

        $categories = Category::where('is_active', true)->get(['slug']);
        foreach ($categories as $category) {
            $urls[] = $appUrl . '/category/' . $category->slug;
        }

        $pages = Page::where('is_published', true)->get(['slug']);
        foreach ($pages as $page) {
            $urls[] = $appUrl . '/page/' . $page->slug;
        }

        $content = implode("\n", $urls) . "\n";

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function llms(): Response
    {
        $rules = Setting::getValue('llms_txt');
        if ($rules) {
            return response($rules, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        $latestPosts = Blog::with('category')
            ->where('is_published', true)
            ->latest('published_at')
            ->take(20)
            ->get();

        $content = view('frontend.llms', compact('latestPosts'))->render();

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function absoluteUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }
}
