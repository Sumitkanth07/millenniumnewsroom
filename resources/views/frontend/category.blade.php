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
                    <img src="{{ asset($image) }}" alt="{{ $post->featured_image_alt ?: $post->title }}" loading="lazy" decoding="async">
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
@endsection
