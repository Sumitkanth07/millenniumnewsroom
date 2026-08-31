<?php

namespace App\Services;

class MediaEmbedSanitizer
{
    /**
     * List of allowlisted domains for <iframe> embeds.
     */
    protected static array $allowedIframeDomains = [
        'youtube.com',
        'www.youtube.com',
        'youtu.be',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com',
        'player.vimeo.com',
        'vimeo.com',
        'spotify.com',
        'open.spotify.com',
        'soundcloud.com',
        'w.soundcloud.com',
        'facebook.com',
        'www.facebook.com',
        'instagram.com',
        'www.instagram.com',
        'twitter.com',
        'x.com',
        'platform.twitter.com',
    ];

    /**
     * Process, sanitize, and transform article content for rich media embeds.
     */
    public static function process(?string $content): array
    {
        if (empty($content)) {
            return [
                'html' => '',
                'blocks' => [],
                'has_twitter' => false,
                'has_instagram' => false,
                'has_facebook' => false,
                'has_youtube' => false,
            ];
        }

        $html = $content;

        // 1. Unescape HTML entities for known embed tags if they were escaped by editor/visual paste
        $html = static::unescapeEmbedTags($html);

        // 2. Convert standalone YouTube URLs into responsive iframe embeds
        $html = static::convertYouTubeUrls($html);

        // 3. Remove dangerous scripts and invalid iframe domains
        $html = static::sanitizeHtml($html);

        // 4. Wrap standalone iframe embeds in responsive containers
        $html = static::wrapResponsiveIframes($html);

        // 5. Detect presence of social embed providers for dynamic script loading
        $hasTwitter = (bool) preg_match('/class=["\'][^"\']*twitter-tweet[^"\']*["\']|twitter\.com\/|x\.com\//i', $html);
        $hasInstagram = (bool) preg_match('/class=["\'][^"\']*instagram-media[^"\']*["\']|instagram\.com\//i', $html);
        $hasFacebook = (bool) preg_match('/class=["\'][^"\']*fb-post[^"\']*["\']|class=["\'][^"\']*fb-video[^"\']*["\']|facebook\.com\//i', $html);
        $hasYoutube = (bool) preg_match('/youtube-nocookie\.com|youtube\.com|youtu\.be/i', $html);

        // 6. Split cleanly into block elements for ad insertion
        $blocks = static::splitBlocks($html);

        return [
            'html' => $html,
            'blocks' => $blocks,
            'has_twitter' => $hasTwitter,
            'has_instagram' => $hasInstagram,
            'has_facebook' => $hasFacebook,
            'has_youtube' => $hasYoutube,
        ];
    }

    /**
     * Unescape escaped embed tags like &lt;iframe...&gt; or &lt;blockquote...&gt;
     */
    protected static function unescapeEmbedTags(string $html): string
    {
        // Decode &lt;iframe ... &gt;&lt;/iframe&gt;
        $html = preg_replace_callback('/&lt;\s*(iframe|blockquote|div)\b([^&]*?)&gt;(.*?)&lt;\/\s*\1\s*&gt;/is', function ($matches) {
            $tag = strtolower($matches[1]);
            $attrs = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $inner = html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return "<{$tag}{$attrs}>{$inner}</{$tag}>";
        }, $html);

        // Decode self-closing or unclosed &lt;iframe ... &gt;
        $html = preg_replace_callback('/&lt;\s*(iframe)\b([^&]*?)&gt;/is', function ($matches) {
            $attrs = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return "<iframe{$attrs}>";
        }, $html);

        return $html;
    }

    /**
     * Convert standalone YouTube URLs into responsive embed player containers.
     */
    protected static function convertYouTubeUrls(string $html): string
    {
        $patterns = [
            '/(?:<p>|<div[^>]*>|\b|^)\s*(?:<a[^>]*href=["\'])?(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([A-Za-z0-9_\-]+)(?:&[^\s<"]*)?(?:["\'][^>]*>.*?<\/a>)?\s*(?:<\/p>|<\/div>|\b|$)/i',
            '/(?:<p>|<div[^>]*>|\b|^)\s*(?:<a[^>]*href=["\'])?(?:https?:\/\/)?youtu\.be\/([A-Za-z0-9_\-]+)(?:\?[^\s<"]*)?(?:["\'][^>]*>.*?<\/a>)?\s*(?:<\/p>|<\/div>|\b|$)/i',
        ];

        foreach ($patterns as $pattern) {
            $html = preg_replace_callback($pattern, function ($matches) {
                $videoId = $matches[1];
                return '<div class="media-embed-responsive youtube-embed"><iframe src="https://www.youtube-nocookie.com/embed/' . $videoId . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe></div>';
            }, $html);
        }

        return $html;
    }

    /**
     * Sanitize HTML content: remove unsafe scripts, dangerous attributes, and non-allowlisted iframes.
     */
    protected static function sanitizeHtml(string $html): string
    {
        // Remove all inline <script> tags and contents
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        // Remove <object>, <embed>, <applet>, <form>, <input>, <button>
        $html = preg_replace('/<\/?(object|embed|applet|form|input|button|select|option)\b[^>]*>/is', '', $html);

        // Strip inline event attributes (onclick, onerror, onload, etc.)
        $html = preg_replace_callback('/<([a-z1-6]+)\s+([^>]+)>/i', function ($matches) {
            $tagName = strtolower($matches[1]);
            $attrs = $matches[2];

            $attrs = preg_replace('/\s*on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $attrs);
            $attrs = preg_replace('/\s*(href|src)\s*=\s*(?:"javascript:[^"]*"|\'javascript:[^\']*\'|javascript:[^\s>]+)/i', '', $attrs);

            return "<{$tagName} {$attrs}>";
        }, $html);

        // Filter <iframe> tags against allowlisted domains
        $html = preg_replace_callback('/<iframe\b([^>]*+)>(?:.*?<\/iframe>)?/is', function ($matches) {
            $attrString = $matches[1];

            if (!preg_match('/src=["\']([^"\']+)["\']/i', $attrString, $srcMatch)) {
                return '';
            }

            $src = $srcMatch[1];
            $host = parse_url($src, PHP_URL_HOST);

            if (!$host || !static::isDomainAllowed($host)) {
                return '';
            }

            if (!preg_match('/loading=["\']/i', $attrString)) {
                $attrString .= ' loading="lazy"';
            }

            return "<iframe{$attrString}></iframe>";
        }, $html);

        return $html;
    }

    /**
     * Check if a domain host is in the allowlisted domains.
     */
    protected static function isDomainAllowed(string $host): bool
    {
        $host = strtolower(trim($host));

        foreach (static::$allowedIframeDomains as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Wrap iframe elements in responsive containers if not already wrapped.
     */
    protected static function wrapResponsiveIframes(string $html): string
    {
        return preg_replace_callback('/(?:<div class="media-embed-responsive[^>]*">\s*)?(<iframe\b[^>]*+><\/iframe>)(?:\s*<\/div>)?/is', function ($matches) {
            $iframe = $matches[1];
            return '<div class="media-embed-responsive">' . $iframe . '</div>';
        }, $html);
    }

    /**
     * Split HTML cleanly into block elements for safe paragraph rendering and ad insertion.
     */
    public static function splitBlocks(string $html): array
    {
        $html = trim($html);
        if ($html === '') {
            return [];
        }

        $pattern = '/(<(?:p|div|blockquote|figure|section|iframe|table|h[1-6]|ul|ol)\b[^>]*>.*?<\/(?:p|div|blockquote|figure|section|iframe|table|h[1-6]|ul|ol)>)/is';
        
        $chunks = preg_split($pattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $blocks = [];

        foreach ($chunks as $chunk) {
            $trimmed = trim($chunk);
            if ($trimmed !== '') {
                $blocks[] = $trimmed;
            }
        }

        if (empty($blocks)) {
            $blocks[] = $html;
        }

        return $blocks;
    }
}
