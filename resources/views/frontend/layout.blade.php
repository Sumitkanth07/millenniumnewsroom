<!doctype html>
<html lang="en">

<head>
    <!-- Google Consent Mode (v2) Default Settings -->
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        
        try {
            const savedConsent = JSON.parse(localStorage.getItem('millennium_cookie_consent'));
            if (savedConsent) {
                gtag('consent', 'default', {
                    'ad_storage': savedConsent.marketing ? 'granted' : 'denied',
                    'ad_user_data': savedConsent.marketing ? 'granted' : 'denied',
                    'ad_personalization': savedConsent.marketing ? 'granted' : 'denied',
                    'analytics_storage': savedConsent.analytics ? 'granted' : 'denied'
                });
            } else {
                gtag('consent', 'default', {
                    'ad_storage': 'denied',
                    'ad_user_data': 'denied',
                    'ad_personalization': 'denied',
                    'analytics_storage': 'denied'
                });
            }
        } catch(e) {
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'analytics_storage': 'denied'
            });
        }
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NTXXK63L');</script>
    <!-- End Google Tag Manager -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-S4DEZ1T335"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-S4DEZ1T335');
    </script>

    @php
        $appUrl = rtrim(config('app.url'), '/');
        $shareImage = $ogImage ?? ($logo ? url(asset($logo)) : null);
        if ($shareImage && !str_starts_with($shareImage, 'http://') && !str_starts_with($shareImage, 'https://')) {
            $cleanShare = ltrim($shareImage, '/');
            if (!str_starts_with($cleanShare, 'public/')) {
                $cleanShare = 'public/' . $cleanShare;
            }
            $shareImage = $appUrl . '/' . $cleanShare;
        }
        $shareImageSecure = $shareImage ? preg_replace('#^http://#i', 'https://', $shareImage) : null;
        
        $shareImageMime = 'image/jpeg';
        if ($shareImage) {
            $parsedPath = parse_url($shareImage, PHP_URL_PATH) ?? '';
            $ext = strtolower(pathinfo($parsedPath, PATHINFO_EXTENSION));
            if ($ext === 'webp') {
                $shareImageMime = 'image/webp';
            } elseif ($ext === 'png') {
                $shareImageMime = 'image/png';
            } elseif ($ext === 'gif') {
                $shareImageMime = 'image/gif';
            } elseif ($ext === 'svg') {
                $shareImageMime = 'image/svg+xml';
            }
        }

        $canonical = $canonicalUrl ?? $appUrl.'/'.ltrim(request()->path(), '/');
        
        $logoExt = $logo ? pathinfo($logo, PATHINFO_EXTENSION) : null;
        $logoMime = 'image/png';
        if ($logoExt === 'jpg' || $logoExt === 'jpeg') {
            $logoMime = 'image/jpeg';
        } elseif ($logoExt === 'webp') {
            $logoMime = 'image/webp';
        } elseif ($logoExt === 'svg') {
            $logoMime = 'image/svg+xml';
        } elseif ($logoExt === 'ico') {
            $logoMime = 'image/x-icon';
        }
    @endphp

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <title>{{ $metaTitle ?? $siteTitle }}</title>

    <meta name="description" content="{{ $metaDescription ?? $tagline }}">

    <meta name="robots" content="{{ $robotsMeta ?? 'index,follow' }}">

    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:title" content="{{ $ogTitle ?? $metaTitle ?? $siteTitle }}">

    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? $tagline }}">

    <meta property="og:url" content="{{ $canonical }}">

    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_IN">
    @isset($articlePublishedTime)<meta property="article:published_time" content="{{ $articlePublishedTime }}">@endisset
    @isset($articleModifiedTime)<meta property="article:modified_time" content="{{ $articleModifiedTime }}">@endisset
    @isset($articleAuthor)<meta property="article:author" content="{{ $articleAuthor }}">@endisset

    <meta name="twitter:title" content="{{ $twitterTitle ?? $metaTitle ?? $siteTitle }}">

    <meta name="twitter:description" content="{{ $twitterDescription ?? $metaDescription ?? $tagline }}">

    <meta name="twitter:card" content="summary_large_image">

    @if($shareImage)
        <meta property="og:image" content="{{ $shareImage }}">
        <meta property="og:image:secure_url" content="{{ $shareImageSecure }}">
        <meta property="og:image:type" content="{{ $shareImageMime }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $metaTitle ?? $siteTitle }}">
    @endif

    @if(isset($twitterImage) || $shareImage)
        <meta name="twitter:image" content="{{ $twitterImage ?? $shareImage }}">
    @endif

    <link rel="preload" href="{{ asset('css/news.css') }}?v={{ $assetVersion }}" as="style">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $assetVersion }}">

    <link rel="stylesheet" href="{{ asset('css/news.css') }}?v={{ $assetVersion }}">

    <link rel="preload" href="{{ asset('css/footer.css') }}?v={{ $assetVersion }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/footer.css') }}?v={{ $assetVersion }}"></noscript>

    <style>
        :root{
            --primary:{{ $primaryColor }};
            --secondary:{{ $secondaryColor }};
        }
    </style>

    @if(isset($seoSchemaData))
    <script type="application/ld+json">
    {!! json_encode($seoSchemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif
    @stack('head')
    @if($adsenseClientId = \App\Models\Setting::getValue('adsense_client_id', 'ca-pub-4398486915982313'))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}" crossorigin="anonymous"></script>
    @endif
    {!! \App\Models\Setting::getValue('google_analytics_code', '') !!}
    {!! \App\Models\Setting::getValue('google_tag_manager_code', '') !!}
    {!! \App\Models\Setting::getValue('microsoft_clarity_code', '') !!}
    {!! \App\Models\Setting::getValue('facebook_pixel_code', '') !!}
    {!! \App\Models\Setting::getValue('custom_header_code', '') !!}
    
    <style>
    .portal-ad-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px auto;
        overflow: hidden;
        transition: height 0.3s ease;
    }
    .portal-ad-wrapper:empty {
        display: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    @media (min-width: 992px) {
        .sticky-sidebar-ad {
            position: sticky;
            top: 20px;
            z-index: 100;
        }
    }
    </style>
    <meta name="google-site-verification" content="VxnnInXR42Safm3W-DIKiunWz4sQr5oGcW2SNJHdrMs" />
</head>

<body class="news-body">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NTXXK63L"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

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

            <a href="{{ route('category.show', 'news') }}" class="nav-link {{ request()->is('category/news*') ? 'active' : '' }}">News</a>
            <a href="{{ route('category.show', 'cxo-view') }}" class="nav-link {{ request()->is('category/cxo-view*') ? 'active' : '' }}">CXO View</a>
            <a href="{{ route('category.show', 'technology') }}" class="nav-link {{ request()->is('category/technology*') ? 'active' : '' }}">Technology</a>
            <a href="{{ route('category.show', 'opinion') }}" class="nav-link {{ request()->is('category/opinion*') ? 'active' : '' }}">Opinion</a>
            <a href="{{ route('page.show', 'about-us') }}" class="nav-link {{ request()->is('page/about-us*') ? 'active' : '' }}">About Us</a>
            <a href="{{ route('page.show', 'privacy-policy') }}" class="nav-link {{ request()->is('page/privacy-policy*') ? 'active' : '' }}">Privacy Policy</a>
            <a href="{{ route('page.show', 'terms') }}" class="nav-link {{ request()->is('page/terms*') ? 'active' : '' }}">Terms</a>
            <a href="{{ route('page.show', 'contact') }}" class="nav-link {{ request()->is('page/contact*') ? 'active' : '' }}">Contact</a>

        </nav>

    </header>

    @isset($breakingPosts)

        @if($breakingPosts->isNotEmpty())

            <div class="ticker">

                <strong>Breaking</strong>

                <div class="ticker-track">

                    @foreach($breakingPosts->concat($breakingPosts) as $post)
                        <a href="{{ $post->publicUrl() }}">
                            {{ $post->title }}
                        </a>
                        <span>•</span>
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

            @php
                $footerCategories = \Illuminate\Support\Facades\Cache::remember('footer.categories.frontend', 90, function() {
                    return \App\Models\Category::where('is_active', true)
                        ->whereHas('blogs', fn($q) => $q->published())
                        ->withCount(['blogs' => fn($q) => $q->published()])
                        ->orderByDesc('blogs_count')
                        ->take(10)
                        ->get();
                });
            @endphp
            @foreach($footerCategories as $footerCategory)
                <a href="{{ route('category.show', $footerCategory->slug) }}">
                    {{ $footerCategory->name }}
                </a>
            @endforeach
            <a href="{{ route('categories.index') }}" style="color: #c79a2b; font-weight: bold; margin-top: 6px; display: inline-block;">
                View All Categories &rarr;
            </a>

        </div>

        <div class="footer-column">

            <h3>Company & Policies</h3>

            <a href="{{ route('page.show', 'about-us') }}">
                About Us
            </a>

            <a href="{{ route('page.show', 'contact') }}">
                Contact
            </a>

            <a href="{{ route('page.show', 'privacy-policy') }}">
                Privacy Policy
            </a>

            <a href="{{ route('page.show', 'terms') }}">
                Terms & Conditions
            </a>

            <a href="{{ route('page.show', 'disclaimer') }}">
                Disclaimer
            </a>

            <a href="{{ route('page.show', 'editorial-policy') }}">
                Editorial Policy
            </a>

            <a href="{{ route('page.show', 'fact-checking-policy') }}">
                Fact Checking Policy
            </a>

            <a href="{{ route('page.show', 'corrections-policy') }}">
                Corrections Policy
            </a>

            <a href="{{ route('page.show', 'cookie-policy') }}">
                Cookie Policy
            </a>

            <a href="javascript:void(0)" onclick="openCookieConsentSettings()" style="cursor: pointer;">
                Cookie Preferences
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

    <!-- Cookie Consent Popup -->

    <div id="cookieConsentBanner" class="cookie-banner" style="display: none; position: fixed; bottom: 24px; right: 24px; left: 24px; max-width: 500px; background: #1f1a12; color: #efe5d1; border: 2px solid #c79a2b; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 10000; padding: 20px; box-sizing: border-box;">
        <h4 style="margin: 0 0 10px 0; color: #c79a2b; font-family: Georgia, serif; font-size: 18px; font-weight: bold;">Cookie Preferences</h4>
        <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.5; opacity: 0.95;">
            We use cookies to personalize content, analyze our traffic, and serve targeted ads. Read our <a href="{{ route('page.show', 'cookie-policy') }}" style="color: #c79a2b; text-decoration: underline; font-weight: bold;">Cookie Policy</a> for details.
        </p>
        
        <!-- Accordion Settings -->
        <div id="cookieConsentSettings" style="display: none; margin-bottom: 16px; border-top: 1px solid rgba(199,154,43,0.3); padding-top: 12px; font-size: 13px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label style="font-weight: bold; display: flex; align-items: center; gap: 6px;">
                    <input type="checkbox" id="cookieNecessary" checked disabled style="accent-color: #c79a2b;"> Necessary
                </label>
                <span style="color: #8e7d61; font-size: 11px;">Required for site to function</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label style="font-weight: bold; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" id="cookieAnalytics" checked style="accent-color: #c79a2b;"> Analytics
                </label>
                <span style="color: #8e7d61; font-size: 11px;">Google Analytics (traffic audit)</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-weight: bold; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" id="cookieMarketing" checked style="accent-color: #c79a2b;"> Marketing & Ads
                </label>
                <span style="color: #8e7d61; font-size: 11px;">Google AdSense & partners</span>
            </div>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end;">
            <button id="btnCookieSettings" style="background: transparent; color: #c79a2b; border: 1px solid #c79a2b; border-radius: 4px; padding: 8px 12px; font-size: 13px; font-weight: bold; cursor: pointer; transition: background 0.2s;">Cookie Settings</button>
            <button id="btnRejectCookies" style="background: rgba(255,255,255,0.05); color: #efe5d1; border: 1px solid rgba(255,255,255,0.15); border-radius: 4px; padding: 8px 12px; font-size: 13px; font-weight: bold; cursor: pointer; transition: background 0.2s;">Reject Non-Essential</button>
            <button id="btnAcceptAllCookies" style="background: #c79a2b; color: #1f1a12; border: 0; border-radius: 4px; padding: 8px 16px; font-size: 13px; font-weight: bold; cursor: pointer; transition: background 0.2s;">Accept All</button>
        </div>
    </div>

    <script>
        const CONSENT_KEY = 'millennium_cookie_consent';

        function updateConsentUI(consent) {
            document.getElementById('cookieAnalytics').checked = consent.analytics;
            document.getElementById('cookieMarketing').checked = consent.marketing;
        }

        function triggerGoogleConsentUpdate(analytics, marketing) {
            try {
                if (typeof window.gtag === 'function') {
                    window.gtag('consent', 'update', {
                        'ad_storage': marketing ? 'granted' : 'denied',
                        'ad_user_data': marketing ? 'granted' : 'denied',
                        'ad_personalization': marketing ? 'granted' : 'denied',
                        'analytics_storage': analytics ? 'granted' : 'denied'
                    });
                }
            } catch (e) {
                console.error("Failed to update Google Consent Mode:", e);
            }
        }

        function saveConsentChoice(analytics, marketing) {
            const consent = { necessary: true, analytics, marketing };
            localStorage.setItem(CONSENT_KEY, JSON.stringify(consent));
            triggerGoogleConsentUpdate(analytics, marketing);
            document.getElementById('cookieConsentBanner').style.display = 'none';
        }

        function openCookieConsentSettings() {
            const banner = document.getElementById('cookieConsentBanner');
            const settings = document.getElementById('cookieConsentSettings');
            const btnSettings = document.getElementById('btnCookieSettings');
            
            let savedConsent = { necessary: true, analytics: true, marketing: true };
            try {
                const stored = localStorage.getItem(CONSENT_KEY);
                if (stored) {
                    savedConsent = JSON.parse(stored);
                }
            } catch (e) {}
            
            updateConsentUI(savedConsent);
            
            banner.style.display = 'block';
            settings.style.display = 'block';
            btnSettings.textContent = 'Save Settings';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const banner = document.getElementById('cookieConsentBanner');
            const settings = document.getElementById('cookieConsentSettings');
            const btnSettings = document.getElementById('btnCookieSettings');
            const btnReject = document.getElementById('btnRejectCookies');
            const btnAccept = document.getElementById('btnAcceptAllCookies');

            let consent = null;
            try {
                const stored = localStorage.getItem(CONSENT_KEY);
                if (stored) {
                    consent = JSON.parse(stored);
                }
            } catch(e) {}

            if (!consent) {
                banner.style.display = 'block';
            } else {
                triggerGoogleConsentUpdate(consent.analytics, consent.marketing);
            }

            btnAccept.addEventListener('click', () => {
                saveConsentChoice(true, true);
            });

            btnReject.addEventListener('click', () => {
                saveConsentChoice(false, false);
            });

            btnSettings.addEventListener('click', () => {
                if (settings.style.display === 'none') {
                    settings.style.display = 'block';
                    btnSettings.textContent = 'Save Settings';
                } else {
                    const analytics = document.getElementById('cookieAnalytics').checked;
                    const marketing = document.getElementById('cookieMarketing').checked;
                    saveConsentChoice(analytics, marketing);
                }
            });
        });
    </script>

    @stack('scripts')

    <!-- Advertisement Impression / Lazy Load Tracker -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const wrapper = entry.target;
                    obs.unobserve(wrapper);
                    
                    const template = wrapper.querySelector('template');
                    if (template) {
                        const clone = template.content.cloneNode(true);
                        wrapper.replaceChildren(clone);
                    }
                    
                    const adId = wrapper.dataset.adId;
                    if (adId) {
                        fetch(`/ads/track-view/${adId}`)
                            .catch(err => console.error('Ad tracking failed:', err));
                    }
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.portal-ad-wrapper').forEach(el => {
            observer.observe(el);
        });
    });
    </script>
</body>
</html>
