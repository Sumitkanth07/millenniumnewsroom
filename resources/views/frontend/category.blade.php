@extends('frontend.layout')
@section('content')
<section class="page-hero"><p class="eyebrow">Category</p><h1>{{ $category->name }}</h1><p>{{ $category->meta_description }}</p></section>
<section class="news-shell content-grid">
    <div class="article-list">
        @forelse($posts as $post)
            @php
                $image = $post->featured_image ?: $post->image;
                if ($image && !str_starts_with($image, 'http') && !str_starts_with($image, '/')) {
                    $image = ltrim($image, '/');
                }
            @endphp
            <article class="list-card">
                @if($image)
                    <img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->featured_image_alt ?: $post->title }}" loading="lazy" decoding="async">
                @endif
                <div><span>{{ optional($post->published_at)->format('M d, Y') }}</span><h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3><p>{{ $post->excerpt }}</p></div>
            </article>
        @empty
            <div class="card empty-state"><h2>No stories yet</h2><p>New articles in {{ $category->name }} will appear here.</p></div>
        @endforelse
        {{ $posts->links() }}
    </div>
    <aside class="sidebar"><h2>Trending</h2>@foreach($trendingPosts as $post)<a class="trend" href="{{ $post->publicUrl() }}"><strong>{{ $loop->iteration }}</strong><span>{{ $post->title }}</span></a>@endforeach</aside>
</section>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            '@id' => url()->current() . '#collectionpage',
            'url' => url()->current(),
            'name' => $metaTitle ?? $category->name,
            'description' => $metaDescription ?? $category->meta_description,
            'publisher' => [
                '@id' => rtrim(config('app.url'), '/') . '#organization'
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => collect($posts->items())->map(function($post, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => $post->publicUrl(),
                        'name' => $post->title
                    ];
                })->values()->all()
            ]
        ],
        [
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
                    'name' => $category->name,
                    'item' => url()->current()
                ]
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
