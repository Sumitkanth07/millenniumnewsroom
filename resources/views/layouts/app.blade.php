<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    <meta charset="utf-8">
    <meta name="google-site-verification" content="VxnnInXR42Safm3W-DIKiunWz4sQr5oGcW2SNJHdrMs" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $metaTitle ?? 'MILLENNIUM NEWSROOM - Business News, Markets and Money')</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? 'Premium business news, markets, money and opinion coverage.')">
    <link rel="canonical" href="{{ $canonicalUrl ?? request()->url() }}">
    <meta name="robots" content="{{ $robotsMeta ?? 'index,follow' }}">

    @if(isset($seoSchemaData))
    <script type="application/ld+json">
    {!! json_encode($seoSchemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif

    @stack('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ $assetVersion ?? time() }}">
    @stack('styles')
</head>
<body class="@yield('body_class', 'news-portal')">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NTXXK63L"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    @include('partials.header')

    <main class="site-main">
        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Cookie Consent Popup -->
    <style>
        .cookie-banner {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
            transition: all 0.3s ease-in-out;
        }
        @media (min-width: 641px) {
            #cookieConsentBanner {
                bottom: 24px !important;
                right: 24px !important;
                left: auto !important;
                width: 450px !important;
            }
        }
        @media (max-width: 640px) {
            #cookieConsentBanner {
                bottom: 16px !important;
                right: 16px !important;
                left: 16px !important;
                width: auto !important;
                max-width: none !important;
            }
        }
    </style>

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
</body>
</html>
