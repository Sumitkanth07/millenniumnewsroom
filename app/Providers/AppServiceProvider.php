<?php

namespace App\Providers;

use App\Models\NavigationItem;
use App\Models\FooterSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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

        return NavigationItem::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($item) use ($categoryUrls) {
                if (isset($categoryUrls[$item->label])) {
                    $item->url = $categoryUrls[$item->label];
                }

                return $item;
            });
    }
}
