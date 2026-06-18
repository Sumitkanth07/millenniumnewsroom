<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | {{ $siteName ?? 'MILLENNIUM NEWSROOM' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('css/news.css') }}?v={{ $assetVersion }}">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <h1><span>MILLENNIUM</span><span>NEWSROOM</span></h1>
        <button class="admin-mode-toggle" type="button" data-admin-theme-toggle style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; margin-bottom: 20px; cursor: pointer; text-align: center; width: 100%;">Toggle mode</button>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.homepage.index') }}">Homepage Sections</a>
        <a href="{{ route('admin.blogs.index') }}">Post Management</a>
        <a href="{{ route('admin.categories.index') }}">Categories</a>
        <a href="{{ route('admin.authors.index') }}">Authors</a>
        <a href="{{ route('admin.pages.index') }}">Pages CMS</a>
        <a href="{{ route('admin.media.index') }}">Media Library</a>
        <a href="{{ route('admin.ads.index') }}">Ad Management</a>
        <a href="{{ route('admin.branding.edit') }}">Branding</a>
        <a href="{{ route('admin.navigation.index') }}">Navigation</a>
        <a href="{{ route('admin.footer.edit') }}">Footer Settings</a>
        <a href="{{ route('admin.redirects.index') }}">Redirects</a>
        <a href="{{ route('admin.seo.index') }}">SEO Manager</a>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button>Logout</button></form>
    </aside>
    <main class="admin-main">
        @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="notice danger">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
    <script src="{{ asset('js/app.js') }}?v={{ $assetVersion }}" defer></script>
    <script>
    (() => {
        const key = 'millennium-admin-theme';
        document.documentElement.dataset.adminTheme = localStorage.getItem(key) || 'dark';
        document.querySelector('[data-admin-theme-toggle]')?.addEventListener('click', () => {
            const next = document.documentElement.dataset.adminTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.adminTheme = next;
            localStorage.setItem(key, next);
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
