<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
    @include('partials.header')

    <main class="site-main">
        @yield('content')
    </main>

    @include('partials.footer')
    @stack('scripts')
</body>
</html>
