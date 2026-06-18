<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
    <meta name="description" content="@yield('meta_description', 'Premium business news, markets, money and opinion coverage.')">
    <title>@yield('title', 'MILLENNIUM NEWSROOM - Business News, Markets and Money')</title>
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
    @stack('scripts')
</body>
</html>
