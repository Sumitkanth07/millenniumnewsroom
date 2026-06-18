@extends('frontend.layout')
@section('content')
<article class="article">
    <nav class="breadcrumb" style="margin-bottom: 20px; font-size: 14px; color: #8e7d61;">
        <a href="{{ route('home') }}" style="color: #c79a2b; text-decoration: none;">Home</a>
        <span style="margin: 0 8px;">/</span>
        <span style="color: #6b5a3e;">{{ $page->title }}</span>
    </nav>

    @if($page->banner_image)<img class="article-image" src="{{ asset($page->banner_image) }}" alt="{{ $page->title }}" loading="eager" decoding="async">@endif
    <h1>{{ $page->title }}</h1>
    <div class="content">{!! $page->content !!}</div>
</article>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    '@id' => url()->current() . '#breadcrumb',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => rtrim(config('app.url'), '/')
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $page->title,
            'item' => url()->current()
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
