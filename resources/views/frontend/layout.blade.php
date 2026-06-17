<!doctype html>
<html lang="en">

<head>
    @php
        $appUrl = rtrim(config('app.url'), '/');
        $shareImage = $ogImage ?? ($logo ? url(asset($logo)) : null);
        if ($shareImage && !str_starts_with($shareImage, 'http://') && !str_starts_with($shareImage, 'https://')) {
            $shareImage = $appUrl.'/'.ltrim($shareImage, '/');
        }
        $canonical = $canonicalUrl ?? $appUrl.'/'.ltrim(request()->path(), '/');
    @endphp

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ $logo ? asset($logo) : asset('favicon.ico') }}">

    <title>{{ $metaTitle ?? $siteTitle }}</title>

    <meta name="description" content="{{ $metaDescription ?? $tagline }}">

    <meta name="robots" content="{{ $robotsMeta ?? 'index,follow' }}">

    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:title" content="{{ $metaTitle ?? $siteTitle }}">

    <meta property="og:description" content="{{ $metaDescription ?? $tagline }}">

    <meta property="og:url" content="{{ $canonical }}">

    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_IN">
    @isset($articlePublishedTime)<meta property="article:published_time" content="{{ $articlePublishedTime }}">@endisset
    @isset($articleModifiedTime)<meta property="article:modified_time" content="{{ $articleModifiedTime }}">@endisset
    @isset($articleAuthor)<meta property="article:author" content="{{ $articleAuthor }}">@endisset

    <meta name="twitter:title" content="{{ $metaTitle ?? $siteTitle }}">

    <meta name="twitter:description" content="{{ $metaDescription ?? $tagline }}">

    <meta name="twitter:card" content="summary_large_image">

    @if($shareImage)
        <meta property="og:image" content="{{ $shareImage }}">
        <meta property="og:image:secure_url" content="{{ $shareImage }}">
        <meta property="og:image:alt" content="{{ $metaTitle ?? $siteTitle }}">
    @endif

    @if($shareImage)
        <meta name="twitter:image" content="{{ $shareImage }}">
    @endif

    <link rel="preload" href="{{ asset('css/news.css') }}?v={{ $assetVersion }}" as="style">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $assetVersion }}">

    <link rel="stylesheet" href="{{ asset('css/news.css') }}?v={{ $assetVersion }}">

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}?v={{ $assetVersion }}">

    <style>
        :root{
            --primary:{{ $primaryColor }};
            --secondary:{{ $secondaryColor }};
        }
    </style>

    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $appUrl.'#organization',
                'name' => $siteName,
                'url' => $appUrl,
                'logo' => $logo ? $appUrl.'/'.ltrim($logo, '/') : $appUrl.'/favicon.ico',
                'description' => $tagline,
            ],
            [
                '@type' => 'WebSite',
                '@id' => $appUrl.'#website',
                'url' => $appUrl,
                'name' => $siteName,
                'publisher' => ['@id' => $appUrl.'#organization'],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $appUrl.'/search?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

</head>

<body class="news-body">

    <div class="reading-progress" id="readingProgress"></div>

    <div class="utility-bar">

        <a href="{{ route('search') }}">Search</a>

        <button class="mode-toggle" type="button" data-theme-toggle>
            Dark
        </button>

        <a href="{{ route('login') }}">Login</a>

    </div>

    <header class="site-header news-header">

        <a class="brand" href="{{ route('home') }}">

            @if($logo)

                <img 
                    src="{{ asset($logo) }}"
                    alt="{{ $siteName }} logo"
                    width="42"
                    height="42">

            @else

                <span class="leaf-logo">M</span>

            @endif

            <span>{{ $siteName }}</span>

        </a>

        <button class="nav-toggle" type="button" aria-label="Open menu" onclick="document.querySelector('.site-nav').classList.toggle('open')">
            Menu
        </button>

        <nav class="site-nav mega-nav">

            <a href="{{ route('category.show', 'news') }}" class="nav-link">News</a>
            <a href="{{ route('category.show', 'markets') }}" class="nav-link">Markets</a>
            <a href="{{ route('category.show', 'technology') }}" class="nav-link">Technology</a>
            <a href="{{ route('category.show', 'opinion') }}" class="nav-link">Opinion</a>
            <a href="{{ route('page.show', 'about-us') }}" class="nav-link">About Us</a>
            <a href="{{ route('page.show', 'privacy-policy') }}" class="nav-link">Privacy Policy</a>
            <a href="{{ route('page.show', 'terms') }}" class="nav-link">Terms</a>
            <a href="{{ route('page.show', 'contact') }}" class="nav-link">Contact</a>

        </nav>

    </header>

    @isset($breakingPosts)

        @if($breakingPosts->isNotEmpty())

            <div class="ticker">

                <strong>Breaking</strong>

                <div class="ticker-track">

                    @foreach($breakingPosts as $post)

                        <a href="{{ $post->publicUrl() }}">
                            {{ $post->title }}
                        </a>

                        @if(!$loop->last)
                            <span>•</span>
                        @endif

                    @endforeach

                </div>

            </div>

        @endif

    @endisset

    <main>
        @yield('content')
    </main>

    <footer class="footer site-footer news-footer">

        <div class="footer-column">

            <strong>
                {{ $footerSetting->company_name ?? $siteName }}
            </strong>

            <span>
                Sharp, fast and independent coverage across business, politics, technology, markets and culture.
            </span>

            <small>
                {{ $footerSetting->copyright_text }}
            </small>

        </div>

        <div class="footer-column">

            <h3>Categories</h3>

            <a href="{{ route('category.show', \Illuminate\Support\Str::slug('Markets')) }}">
                Markets
            </a>
            <a href="{{ route('category.show', \Illuminate\Support\Str::slug('Technology')) }}">
                Technology
            </a>
            <a href="{{ route('category.show', \Illuminate\Support\Str::slug('Opinion')) }}">
                Opinion
            </a>

        </div>

        <div class="footer-column">

            <h3>Company</h3>

            <a href="{{ route('page.show', 'about-us') }}">
                About us
            </a>

            <a href="{{ route('page.show', 'privacy-policy') }}">
                Privacy Policy
            </a>

            <a href="{{ route('page.show', 'terms') }}">
                Terms
            </a>

            <a href="{{ route('page.show', 'contact') }}">
                Contact
            </a>

            <a href="{{ route('sitemap.page') }}">
                Sitemap
            </a>

        </div>

        <div class="footer-column">

            <h3>Contact</h3>

            @if($footerSetting->email)

                <a href="mailto:{{ $footerSetting->email }}">
                    {{ $footerSetting->email }}
                </a>

            @endif

            @if($footerSetting->phone)

                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerSetting->phone) }}">
                    {{ $footerSetting->phone }}
                </a>

            @endif

            @if($footerSetting->address)

                <span>{{ $footerSetting->address }}</span>

            @endif

            @if(!empty($footerSetting->social_links))
                <span class="socials">
                    @foreach($footerSetting->social_links as $social)
                        @php
                            $socialParts = array_pad(explode('|', $social, 2), 2, '#');
                            $label = $socialParts[0];
                            $url = $socialParts[1];
                        @endphp
                        <a href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a>@if(!$loop->last) &middot; @endif
                    @endforeach
                </span>
            @endif

        </div>

    </footer>

    <script src="{{ asset('js/app.js') }}?v={{ $assetVersion }}" defer></script>

    <script>
    (() => {

        const key = 'millennium-theme';

        const apply = theme =>
            document.documentElement.dataset.theme = theme;

        apply(localStorage.getItem(key) || 'light');

        const navToggle = document.querySelector('.nav-toggle');
        const siteNav = document.querySelector('.site-nav');
        if (navToggle && siteNav) {
            navToggle.addEventListener('click', () => {
                siteNav.classList.toggle('open');
            });
        }

        document.querySelectorAll('[data-theme-toggle]')
            .forEach(btn => btn.addEventListener('click', () => {

                const next =
                    document.documentElement.dataset.theme === 'dark'
                    ? 'light'
                    : 'dark';

                localStorage.setItem(key, next);

                apply(next);

                btn.textContent =
                    next === 'dark'
                    ? 'Light'
                    : 'Dark';

            }));

        let ticking = false;

        window.addEventListener('scroll', () => {

            if (ticking) return;

            ticking = true;

            requestAnimationFrame(() => {

                const bar =
                    document.getElementById('readingProgress');

                if (bar) {

                    const max =
                        document.documentElement.scrollHeight - innerHeight;

                    bar.style.width =
                        max > 0
                        ? (scrollY / max * 100) + '%'
                        : '0%';
                }

                ticking = false;

            });

        }, { passive:true });

    })();
    </script>

    @stack('scripts')

</body>
</html>
