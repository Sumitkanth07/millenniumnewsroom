<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.index', [

            'blogs' => Blog::with(['category', 'author'])
                ->where('is_published', true)
                ->latest('published_at')
                ->paginate(9),

            'metaTitle' => 'Latest News | MILLENNIUM NEWSROOM',

            'metaDescription' => 'Latest news, analysis and opinion from MILLENNIUM NEWSROOM.',

        ]);
    }

    public function redirectLegacy(Blog $blog)
    {
        return redirect()->to($blog->load('category')->publicUrl(), 301);
    }

    public function show(Category $category, Blog $blog)
    {
        abort_unless($blog->is_published, 404);
        abort_unless((int) $blog->category_id === (int) $category->id, 404);

        $blog->increment('views_count');

        $blog->load(['category', 'author', 'tags']);
        $canonicalUrl = $blog->canonical_url ?: $this->absoluteUrl(route('blog.category.show', [
            'category' => $blog->category?->slug ?: 'news',
            'blog' => $blog->slug,
        ], false));
        $description = $blog->meta_description ?: ($blog->excerpt ?: (string) str($blog->content)->stripTags()->limit(160));
        $image = $this->absoluteAsset($blog->featured_image ?: $blog->image);

        return view('blog.show', [

            'blog' => $blog,

            'relatedPosts' => Blog::with([
                    'category',
                    'author'
                ])
                ->where('is_published', true)
                ->whereKeyNot($blog->id)
                ->latest('published_at')
                ->take(4)
                ->get(),

            'trendingPosts' => Blog::with('category')
                ->where('is_published', true)
                ->orderByDesc('views_count')
                ->take(5)
                ->get(),

            'metaTitle' => $blog->meta_title
                ?: $blog->title.' | MILLENNIUM NEWSROOM',

            'metaDescription' => $blog->meta_description
                ?: $description,

            'robotsMeta' => $blog->robots_meta
                ?: 'index,follow',

            'canonicalUrl' => $blog->canonical_url
                ?: $canonicalUrl,

            'ogType' => 'article',

            'ogImage' => $image,

            'articlePublishedTime' => optional($blog->published_at)->toAtomString(),

            'articleModifiedTime' => optional($blog->updated_at)->toAtomString(),

            'articleAuthor' => $blog->author?->name ?? 'MILLENNIUM NEWSROOM Desk',

            'articleSchema' => $this->articleSchema($blog, $canonicalUrl, $description, $image),

        ]);
    }

    private function absoluteAsset(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $this->absoluteUrl('/public/'.ltrim($path, '/'));
    }

    private function absoluteUrl(string $path): string
    {
        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }

    private function articleSchema(Blog $blog, string $canonicalUrl, string $description, ?string $image): array
    {
        $authorName = $blog->author?->name ?? 'MILLENNIUM NEWSROOM Desk';

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => ['NewsArticle', 'Article'],
                    '@id' => $canonicalUrl.'#article',
                    'mainEntityOfPage' => $canonicalUrl,
                    'headline' => $blog->title,
                    'description' => $description,
                    'image' => $image ? [$image] : [],
                    'datePublished' => optional($blog->published_at)->toAtomString(),
                    'dateModified' => optional($blog->updated_at)->toAtomString(),
                    'articleSection' => $blog->category?->name,
                    'keywords' => $blog->tags->pluck('name')->implode(', '),
                    'wordCount' => str_word_count(strip_tags($blog->content)),
                    'author' => ['@id' => $canonicalUrl.'#author'],
                    'publisher' => ['@id' => $this->absoluteUrl('/').'#organization'],
                    'isAccessibleForFree' => true,
                ],
                [
                    '@type' => 'Person',
                    '@id' => $canonicalUrl.'#author',
                    'name' => $authorName,
                    'description' => $blog->author?->bio,
                    'image' => $this->absoluteAsset($blog->author?->image),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl.'#breadcrumb',
                    'itemListElement' => array_values(array_filter([
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => $this->absoluteUrl('/'),
                        ],
                        $blog->category ? [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $blog->category->name,
                            'item' => $this->absoluteUrl('/category/'.$blog->category->slug),
                        ] : null,
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $blog->title,
                            'item' => $canonicalUrl,
                        ],
                    ])),
                ],
            ],
        ];
    }
}
