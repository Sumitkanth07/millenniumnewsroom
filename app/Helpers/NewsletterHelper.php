<?php

namespace App\Helpers;

use App\Models\Blog;
use App\Models\NewsletterSetting;

class NewsletterHelper
{
    public static function getAbsoluteImageUrl(?string $image): string
    {
        $appUrl = rtrim((string) config('app.url', 'https://millenniumnewsroom.com'), '/');
        
        if (empty($image)) {
            return $appUrl . '/images/default-newsletter.jpg';
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return preg_replace('#^http://#i', 'https://', $image);
        }

        $cleanPath = ltrim($image, '/');
        if (!str_starts_with($cleanPath, 'public/')) {
            $cleanPath = 'public/' . $cleanPath;
        }

        return $appUrl . '/' . $cleanPath;
    }

    public static function getAbsoluteArticleUrl(Blog $blog): string
    {
        $appUrl = rtrim((string) config('app.url', 'https://millenniumnewsroom.com'), '/');
        $categorySlug = $blog->category?->slug ?: 'news';
        
        return $appUrl . '/' . $categorySlug . '/' . $blog->slug;
    }
}
