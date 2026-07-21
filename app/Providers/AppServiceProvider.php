<?php

namespace App\Providers;

use App\Models\NavigationItem;
use App\Models\FooterSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') && ! in_array(request()->getHost(), ['127.0.0.1', 'localhost'], true)) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            static $shared = null;

            if ($shared !== null) {
                $view->with($shared);

                return;
            }

            try {
                if (app()->environment('testing')) {
                    throw new \RuntimeException;
                }

                $hasSettings = Schema::hasTable('settings');
                $hasNavigation = Schema::hasTable('navigation_items');
                $hasFooter = Schema::hasTable('footer_settings');
                $hasAds = Schema::hasTable('ad_placements');
            } catch (Throwable) {
                $hasSettings = false;
                $hasNavigation = false;
                $hasFooter = false;
                $hasAds = false;
            }

            $shared = [
                'siteName' => $this->brand($hasSettings ? Setting::getValue('site_name', 'MILLENNIUM NEWSROOM') : 'MILLENNIUM NEWSROOM'),
                'siteTitle' => $this->brand($hasSettings ? Setting::getValue('site_title', 'MILLENNIUM NEWSROOM | Professional News Portal') : 'MILLENNIUM NEWSROOM | Professional News Portal'),
                'tagline' => $hasSettings ? Setting::getValue('tagline', 'Business, markets and technology journalism') : 'Business, markets and technology journalism',
                'primaryColor' => $hasSettings ? Setting::getValue('primary_color', '#1f1a12') : '#1f1a12',
                'secondaryColor' => $hasSettings ? Setting::getValue('secondary_color', '#c79a2b') : '#c79a2b',
                'logo' => $hasSettings ? Setting::getValue('logo') : null,
                'assetVersion' => $this->assetVersion(),
                'navigationItems' => $hasNavigation ? $this->navigationItems() : collect(),
                'footerSetting' => $hasFooter ? FooterSetting::current() : new FooterSetting([
                'company_name' => 'MILLENNIUM NEWSROOM',
                'email' => 'info@millenniumnewsroom.com',
                'phone' => '+91 9876543210',
                'address' => 'New Delhi, India',
                'copyright_text' => '(c) '.date('Y').' MILLENNIUM NEWSROOM. All rights reserved.',
                ]),
                'ads' => $hasAds ? \App\Models\AdPlacement::where('is_active', true)->get()->keyBy('key') : collect(),
            ];

            // Resolve SEO settings dynamically
            $seo = null;
            try {
                if ($hasSettings && Schema::hasTable('seo_settings')) {
                    $path = '/' . trim(request()->path(), '/');
                    // Check path first
                    $seo = \App\Models\SeoSetting::where('seoable_type', 'Path:' . $path)
                        ->where('seoable_id', 0)
                        ->first();

                    // If not found, check bound parameters
                    if (!$seo && request()->route()) {
                        foreach (request()->route()->parameters() as $param) {
                            if ($param instanceof \Illuminate\Database\Eloquent\Model && method_exists($param, 'seoSetting')) {
                                $seo = $param->seoSetting;
                                break;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                $seo = null;
            }

            if ($seo) {
                if ($seo->meta_title) {
                    $shared['metaTitle'] = $seo->meta_title;
                }
                if ($seo->meta_description) {
                    $shared['metaDescription'] = $seo->meta_description;
                }
                if ($seo->robots_meta) {
                    $shared['robotsMeta'] = $seo->robots_meta;
                }
                if ($seo->canonical_url) {
                    $shared['canonicalUrl'] = $seo->canonical_url;
                    $shared['canonical'] = $seo->canonical_url;
                }
                if ($seo->og_title) {
                    $shared['ogTitle'] = $seo->og_title;
                }
                if ($seo->og_description) {
                    $shared['ogDescription'] = $seo->og_description;
                }

                $shareImage = $seo->og_image;
                if ($shareImage) {
                    if (!str_starts_with($shareImage, 'http://') && !str_starts_with($shareImage, 'https://')) {
                        $shareImage = rtrim((string) config('app.url'), '/').'/'.ltrim($shareImage, '/');
                    }
                    $shared['ogImage'] = $shareImage;
                }

                // Retrieve Twitter overrides from JSON data
                if (is_array($seo->schema_data)) {
                    if (!empty($seo->schema_data['twitter_title'])) {
                        $shared['twitterTitle'] = $seo->schema_data['twitter_title'];
                    }
                    if (!empty($seo->schema_data['twitter_description'])) {
                        $shared['twitterDescription'] = $seo->schema_data['twitter_description'];
                    }
                    if (!empty($seo->schema_data['twitter_image'])) {
                        $twImg = $seo->schema_data['twitter_image'];
                        if (!str_starts_with($twImg, 'http://') && !str_starts_with($twImg, 'https://')) {
                            $twImg = rtrim((string) config('app.url'), '/').'/'.ltrim($twImg, '/');
                        }
                        $shared['twitterImage'] = $twImg;
                    }
                }

                $shared['seoSchema'] = $seo;
            }

            // Extract parameters/view variables for fallback checks
            $viewVariables = $view->getData();
            $fallbackTitle = '';
            $fallbackDescription = '';

            if (isset($viewVariables['blog']) && $viewVariables['blog'] instanceof \App\Models\Blog) {
                $blog = $viewVariables['blog'];
                $fallbackTitle = $blog->title;
                $fallbackDescription = $blog->excerpt ?: (string) str($blog->content)->stripTags()->limit(155);
            } elseif (isset($viewVariables['page']) && $viewVariables['page'] instanceof \App\Models\Page) {
                $page = $viewVariables['page'];
                $fallbackTitle = $page->title;
                $fallbackDescription = (string) str($page->content)->stripTags()->limit(155);
            } elseif (isset($viewVariables['category']) && $viewVariables['category'] instanceof \App\Models\Category) {
                $category = $viewVariables['category'];
                $fallbackTitle = $category->name;
                $fallbackDescription = $category->description ?: $category->meta_description;
            } elseif (isset($viewVariables['author']) && $viewVariables['author'] instanceof \App\Models\Author) {
                $author = $viewVariables['author'];
                $fallbackTitle = $author->name . ' | Author Profile';
                $fallbackDescription = $author->bio;
            }

            // Merge shared/overridden values
            $title = $shared['metaTitle'] ?? $viewVariables['metaTitle'] ?? null;
            if (empty($title)) {
                $title = $fallbackTitle ? ($fallbackTitle . ' | ' . $shared['siteName']) : $shared['siteTitle'];
            }
            $shared['metaTitle'] = $title;

            $desc = $shared['metaDescription'] ?? $viewVariables['metaDescription'] ?? null;
            if (empty($desc)) {
                $desc = $fallbackDescription ?: $shared['tagline'];
            }
            $shared['metaDescription'] = (string) str($desc)->stripTags()->limit(155);

            $canonical = $shared['canonicalUrl'] ?? $viewVariables['canonicalUrl'] ?? null;
            if (empty($canonical)) {
                $canonical = request()->url();
            }
            $shared['canonicalUrl'] = $canonical;
            $shared['canonical'] = $canonical;

            $robots = $shared['robotsMeta'] ?? $viewVariables['robotsMeta'] ?? null;
            if (empty($robots)) {
                $robots = 'index,follow,max-image-preview:large';
            }
            if ($robots === 'index,follow') {
                $robots = 'index,follow,max-image-preview:large';
            }
            $shared['robotsMeta'] = $robots;

            // OpenGraph & Twitter Cards fallback
            if (!isset($shared['ogTitle'])) {
                $shared['ogTitle'] = $shared['metaTitle'];
            }
            if (!isset($shared['ogDescription'])) {
                $shared['ogDescription'] = $shared['metaDescription'];
            }
            if (!isset($shared['twitterTitle'])) {
                $shared['twitterTitle'] = $shared['metaTitle'];
            }
            if (!isset($shared['twitterDescription'])) {
                $shared['twitterDescription'] = $shared['metaDescription'];
            }

            // Build Unified Schema Graph
            $appUrl = str_replace('http://', 'https://', rtrim((string) config('app.url'), '/'));
            $logo = $shared['logo'];
            
            $schemaGraph = [
                [
                    '@type' => ['Organization', 'NewsMediaOrganization'],
                    '@id' => $appUrl . '#organization',
                    'name' => $shared['siteName'],
                    'url' => $appUrl,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logo ? $appUrl . '/' . ltrim($logo, '/') : $appUrl . '/favicon.ico'
                    ],
                    'description' => $shared['tagline'],
                    'publishingPrinciples' => $appUrl . '/page/editorial-policy',
                    'correctionsPolicy' => $appUrl . '/page/corrections-policy',
                    'ethicsPolicy' => $appUrl . '/page/editorial-policy',
                    'diversityPolicy' => $appUrl . '/page/fact-checking-policy',
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $appUrl . '#website',
                    'url' => $appUrl,
                    'name' => $shared['siteName'],
                    'publisher' => ['@id' => $appUrl . '#organization'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => $appUrl . '/search?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ]
            ];

            // 1. Article Page Schema
            if (isset($viewVariables['blog']) && $viewVariables['blog'] instanceof \App\Models\Blog) {
                $blog = $viewVariables['blog'];
                $canonicalUrl = $shared['canonicalUrl'];
                $image = $blog->featured_image ?: $blog->image;
                if ($image) {
                    if (!str_starts_with($image, 'http://') && !str_starts_with($image, 'https://')) {
                        $image = $appUrl . '/' . ltrim($image, '/');
                    }
                }
                $authorName = $blog->author?->name ?? 'MILLENNIUM NEWSROOM Desk';

                $schemaGraph[] = [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl,
                    'url' => $canonicalUrl,
                    'name' => $shared['metaTitle'],
                    'description' => $shared['metaDescription'],
                    'isPartOf' => ['@id' => $appUrl . '#website'],
                    'primaryImageOfPage' => $image ? ['@id' => $canonicalUrl . '#primaryimage'] : null,
                ];

                if ($image) {
                    $schemaGraph[] = [
                        '@type' => 'ImageObject',
                        '@id' => $canonicalUrl . '#primaryimage',
                        'inLanguage' => 'en-US',
                        'url' => $image,
                    ];
                }

                $schemaGraph[] = [
                    '@type' => ['NewsArticle', 'Article'],
                    '@id' => $canonicalUrl . '#article',
                    'isPartOf' => ['@id' => $canonicalUrl],
                    'mainEntityOfPage' => ['@id' => $canonicalUrl],
                    'headline' => $blog->title,
                    'description' => $shared['metaDescription'],
                    'image' => $image ? ['@id' => $canonicalUrl . '#primaryimage'] : [],
                    'datePublished' => optional($blog->published_at)->toAtomString(),
                    'dateModified' => optional($blog->updated_at)->toAtomString(),
                    'articleSection' => $blog->category?->name,
                    'keywords' => $blog->tags ? $blog->tags->pluck('name')->implode(', ') : '',
                    'wordCount' => str_word_count(strip_tags($blog->content)),
                    'author' => ['@id' => $canonicalUrl . '#author'],
                    'publisher' => ['@id' => $appUrl . '#organization'],
                    'isAccessibleForFree' => true,
                ];

                $schemaGraph[] = [
                    '@type' => 'Person',
                    '@id' => $canonicalUrl . '#author',
                    'name' => $authorName,
                    'description' => $blog->author?->bio,
                    'image' => $blog->author?->image ? (str_starts_with($blog->author->image, 'http') ? $blog->author->image : $appUrl . '/' . ltrim($blog->author->image, '/')) : null,
                    'url' => $blog->author ? $appUrl . '/author/' . $blog->author->slug : null,
                ];

                $breadcrumbElements = [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => $appUrl,
                    ]
                ];
                if ($blog->category) {
                    $breadcrumbElements[] = [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $blog->category->name,
                        'item' => $appUrl . '/category/' . $blog->category->slug,
                    ];
                }
                $breadcrumbElements[] = [
                    '@type' => 'ListItem',
                    'position' => count($breadcrumbElements) + 1,
                    'name' => $blog->title,
                    'item' => $canonicalUrl,
                ];

                $schemaGraph[] = [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl . '#breadcrumb',
                    'itemListElement' => $breadcrumbElements,
                ];
            }
            // 2. Category Page Schema
            elseif (isset($viewVariables['category']) && $viewVariables['category'] instanceof \App\Models\Category) {
                $category = $viewVariables['category'];
                $canonicalUrl = $shared['canonicalUrl'];
                $postsList = $viewVariables['posts'] ?? null;

                $itemListElement = [];
                if ($postsList) {
                    foreach ($postsList as $index => $post) {
                        $itemListElement[] = [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'url' => $post->publicUrl(),
                            'name' => $post->title
                        ];
                    }
                }

                $schemaGraph[] = [
                    '@type' => 'CollectionPage',
                    '@id' => $canonicalUrl . '#collectionpage',
                    'url' => $canonicalUrl,
                    'name' => $shared['metaTitle'],
                    'description' => $shared['metaDescription'],
                    'publisher' => ['@id' => $appUrl . '#organization'],
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'itemListElement' => $itemListElement
                    ]
                ];

                $schemaGraph[] = [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl . '#breadcrumb',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => $appUrl
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $category->name,
                            'item' => $canonicalUrl
                        ]
                    ]
                ];
            }
            // 3. Author Page Schema
            elseif (isset($viewVariables['author']) && $viewVariables['author'] instanceof \App\Models\Author) {
                $author = $viewVariables['author'];
                $canonicalUrl = $shared['canonicalUrl'];

                $schemaGraph[] = [
                    '@type' => 'ProfilePage',
                    '@id' => $canonicalUrl . '#profilepage',
                    'url' => $canonicalUrl,
                    'name' => $shared['metaTitle'],
                    'mainEntity' => ['@id' => $canonicalUrl . '#author']
                ];

                $schemaGraph[] = [
                    '@type' => 'Person',
                    '@id' => $canonicalUrl . '#author',
                    'name' => $author->name,
                    'description' => $author->bio,
                    'image' => $author->image ? (str_starts_with($author->image, 'http') ? $author->image : $appUrl . '/' . ltrim($author->image, '/')) : null,
                    'jobTitle' => $author->designation ?: 'Contributor',
                    'worksFor' => ['@id' => $appUrl . '#organization'],
                    'sameAs' => collect($author->social_links)->map(function($social) {
                        return array_pad(explode('|', $social, 2), 2, '#')[1];
                    })->filter(fn($url) => $url !== '#')->values()->all()
                ];
            }
            // 4. Static Page Schema
            elseif (isset($viewVariables['page']) && $viewVariables['page'] instanceof \App\Models\Page) {
                $page = $viewVariables['page'];
                $canonicalUrl = $shared['canonicalUrl'];

                $schemaGraph[] = [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl . '#webpage',
                    'url' => $canonicalUrl,
                    'name' => $shared['metaTitle'],
                    'description' => $shared['metaDescription'],
                    'isPartOf' => ['@id' => $appUrl . '#website'],
                ];

                $schemaGraph[] = [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl . '#breadcrumb',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => $appUrl
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $page->title,
                            'item' => $canonicalUrl
                        ]
                    ]
                ];
            }
            // 5. Article Array Schema (Legacy/Mock Articles Support)
            elseif (isset($viewVariables['article']) && is_array($viewVariables['article'])) {
                $art = $viewVariables['article'];
                $canonicalUrl = $shared['canonicalUrl'];
                $headline = $art['headline'] ?? '';
                $image = $art['image'] ?? '';
                $publishedAt = $art['published_at'] ?? '';
                $updatedAt = $art['updated_at'] ?? '';
                $authorName = $art['author']['name'] ?? 'MILLENNIUM NEWSROOM Desk';

                $schemaGraph[] = [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl,
                    'url' => $canonicalUrl,
                    'name' => $headline,
                    'description' => $shared['metaDescription'],
                    'isPartOf' => ['@id' => $appUrl . '#website'],
                    'primaryImageOfPage' => $image ? ['@id' => $canonicalUrl . '#primaryimage'] : null,
                ];

                if ($image) {
                    $schemaGraph[] = [
                        '@type' => 'ImageObject',
                        '@id' => $canonicalUrl . '#primaryimage',
                        'inLanguage' => 'en-US',
                        'url' => $image,
                    ];
                }

                $schemaGraph[] = [
                    '@type' => ['NewsArticle', 'Article'],
                    '@id' => $canonicalUrl . '#article',
                    'isPartOf' => ['@id' => $canonicalUrl],
                    'mainEntityOfPage' => ['@id' => $canonicalUrl],
                    'headline' => $headline,
                    'description' => $shared['metaDescription'],
                    'image' => $image ? ['@id' => $canonicalUrl . '#primaryimage'] : [],
                    'datePublished' => $publishedAt,
                    'dateModified' => $updatedAt,
                    'articleSection' => $art['category'] ?? 'News',
                    'author' => ['@id' => $canonicalUrl . '#author'],
                    'publisher' => ['@id' => $appUrl . '#organization'],
                    'isAccessibleForFree' => true,
                ];

                $schemaGraph[] = [
                    '@type' => 'Person',
                    '@id' => $canonicalUrl . '#author',
                    'name' => $authorName,
                    'description' => $art['author']['bio'] ?? null,
                    'image' => $art['author']['image'] ?? null,
                ];

                $schemaGraph[] = [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl . '#breadcrumb',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => $appUrl,
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $art['category'] ?? 'News',
                            'item' => $appUrl,
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $headline,
                            'item' => $canonicalUrl,
                        ],
                    ],
                ];
            }

            // Push custom schema if configured
            if ($seo && $seo->schema_type && $seo->schema_type !== 'None') {
                if ($seo->schema_type === 'Custom' || $seo->schema_type === 'Custom JSON-LD') {
                    $customSchemaRaw = is_array($seo->schema_data) ? ($seo->schema_data['custom_schema'] ?? '') : $seo->schema_data;
                    if ($customSchemaRaw) {
                        // Strip any Blade tags to prevent Blade PHP rendering issues
                        $customSchemaRaw = preg_replace('/\{\{[^}]*\}\}/', '', $customSchemaRaw);
                        $customSchemaRaw = preg_replace('/\{!![^!]*!!\}/', '', $customSchemaRaw);
                        $decoded = json_decode($customSchemaRaw, true);
                        if (is_array($decoded)) {
                            if (isset($decoded['@context'])) {
                                unset($decoded['@context']);
                            }
                            $schemaGraph[] = $decoded;
                        }
                    }
                } elseif ($seo->schema_type === 'Organization' || $seo->schema_type === 'NewsMediaOrganization') {
                    // Merge with the existing Organization node to prevent duplication
                    if (isset($schemaGraph[0])) {
                        $schemaGraph[0]['@type'] = ['Organization', 'NewsMediaOrganization'];
                        if ($seo->meta_title) {
                            $schemaGraph[0]['name'] = $seo->meta_title;
                        }
                        if ($seo->meta_description) {
                            $schemaGraph[0]['description'] = $seo->meta_description;
                        }
                        if ($seo->canonical_url) {
                            $schemaGraph[0]['url'] = $seo->canonical_url;
                        }
                    }
                } elseif ($seo->schema_type === 'WebSite') {
                    // Merge with the existing WebSite node to prevent duplication
                    if (isset($schemaGraph[1])) {
                        if ($seo->meta_title) {
                            $schemaGraph[1]['name'] = $seo->meta_title;
                        }
                        if ($seo->canonical_url) {
                            $schemaGraph[1]['url'] = $seo->canonical_url;
                        }
                    }
                } else {
                    $schemaGraph[] = [
                        '@type' => $seo->schema_type,
                        'name' => $seo->meta_title ?? $shared['metaTitle'],
                        'description' => $seo->meta_description ?? $shared['metaDescription'],
                        'url' => $seo->canonical_url ?? $shared['canonicalUrl'],
                    ];
                }
            }

            $shared['seoSchemaData'] = [
                '@context' => 'https://schema.org',
                '@graph' => $schemaGraph,
            ];

            $view->with($shared);
        });
    }

    private function assetVersion(): string
    {
        $files = ['css/app.css', 'css/news.css', 'css/footer.css', 'js/app.js'];

        $latest = collect($files)
            ->map(fn ($file) => public_path($file))
            ->filter(fn ($path) => is_file($path))
            ->map(fn ($path) => filemtime($path))
            ->max();

        return (string) ($latest ?: time());
    }

    private function brand(?string $value): string
    {
        return preg_replace('/MILLENI?UM\s*NEWSROOM/i', 'MILLENNIUMNEWSROOM', (string) $value);
    }

    private function navigationItems()
    {
        $categoryUrls = [
            'News' => '/category/news',
            'Markets' => '/category/markets',
            'Technology' => '/category/technology',
            'Companies' => '/category/companies',
            'Politics' => '/category/politics',
            'Opinion' => '/category/opinion',
            'Sports' => '/category/sports',
            'Lifestyle' => '/category/lifestyle',
        ];

        return Cache::remember('navigation.items', 3600, function () use ($categoryUrls) {
            return NavigationItem::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) use ($categoryUrls) {
                    if (isset($categoryUrls[$item->label])) {
                        $item->url = $categoryUrls[$item->label];
                    }

                    return $item;
                });
        });
    }
}
