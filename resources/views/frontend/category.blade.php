@extends('frontend.layout')
@section('content')
<x-ad-slot placement="category_top" label="Category Top Ad" />
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
                    <img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->featured_image_alt ?: $post->title }}" width="800" height="450" loading="lazy" decoding="async">
                @endif
                <div><span>{{ optional($post->published_at)->format('M d, Y') }}</span><h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3><p>{{ $post->excerpt }}</p></div>
            </article>
            
            @if($loop->iteration % 6 === 0)
                <x-ad-slot placement="category_middle" label="Category Middle Ad" />
            @endif
        @empty
            <div class="card empty-state" style="margin-bottom: 40px; text-align: center; padding: 40px 20px;">
                <h2 style="font-family: Georgia, serif; font-size: 24px; color: #c79a2b; margin-bottom: 8px;">No stories yet</h2>
                <p style="color: #8e7d61; font-size: 16px;">New articles in {{ $category->name }} will appear here.</p>
            </div>
            
            @if(isset($fallbackPosts) && $fallbackPosts->isNotEmpty())
                <div class="fallback-section" style="margin-top: 40px; width: 100%;">
                    <h2 style="font-family: Georgia, serif; font-size: 26px; border-bottom: 2px solid #c79a2b; padding-bottom: 8px; margin-bottom: 24px; color: inherit;">Latest News</h2>
                    <div class="responsive-grid-auto" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        @foreach($fallbackPosts as $fallbackPost)
                            @php
                                $fallbackImage = $fallbackPost->featured_image ?: $fallbackPost->image;
                            @endphp
                            <article class="card" style="display: flex; flex-direction: column; height: 100%; padding: 15px;">
                                @if($fallbackImage)
                                    <div style="aspect-ratio: 16/9; overflow: hidden; border-radius: 6px; margin-bottom: 12px; background: #120f0b;">
                                        <img src="{{ $fallbackPost->getThumbnailUrl() }}" alt="{{ $fallbackPost->featured_image_alt ?: $fallbackPost->title }}" width="800" height="450" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                    </div>
                                @endif
                                <div style="display: flex; flex-direction: column; flex: 1;">
                                    <span style="font-size: 11px; text-transform: uppercase; color: #c79a2b; font-weight: 800;">{{ $fallbackPost->category?->name }}</span>
                                    <h3 style="font-family: Georgia, serif; font-size: 18px; margin: 6px 0; line-height: 1.3;"><a href="{{ $fallbackPost->publicUrl() }}" style="color: inherit; text-decoration: none;">{{ $fallbackPost->title }}</a></h3>
                                    <p style="font-size: 13.5px; color: #8e7d61; line-height: 1.4; margin-top: auto;">{{ Str::limit($fallbackPost->excerpt, 90) }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforelse
        
        <x-ad-slot placement="category_bottom" label="Category Bottom Ad" />
        {{ $posts->links() }}
    </div>
    <aside class="sidebar">
        <x-ad-slot placement="sidebar_top" label="Sidebar Top Ad" />
        <h2>Trending</h2>
        @foreach($trendingPosts as $post)
            <a class="trend" href="{{ $post->publicUrl() }}"><strong>{{ $loop->iteration }}</strong><span>{{ $post->title }}</span></a>
        @endforeach
        <x-ad-slot placement="sidebar_bottom" label="Sidebar Bottom Sticky Ad" :sticky="true" />
    </aside>
</section>
@endsection
