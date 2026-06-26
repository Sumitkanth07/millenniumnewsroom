@extends('frontend.layout')

@section('content')
<section class="page-hero" style="padding: 40px 24px; background: rgba(199,154,43,0.05); border-bottom: 1px solid rgba(199,154,43,0.15);">
    <nav class="breadcrumb" style="margin-bottom: 20px; font-size: 14px; color: #8e7d61;">
        <a href="{{ route('home') }}" style="color: #c79a2b; text-decoration: none;">Home</a>
        <span style="margin: 0 8px;">/</span>
        <span style="color: #6b5a3e;">Authors</span>
        <span style="margin: 0 8px;">/</span>
        <span style="color: #6b5a3e;">{{ $author->name }}</span>
    </nav>
    <div style="display: flex; gap: 24px; align-items: center; flex-wrap: wrap;">
        @if($author->image)
            <img src="{{ asset($author->image) }}" alt="{{ $author->name }}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #c79a2b;">
        @else
            <div style="width: 120px; height: 120px; border-radius: 50%; background: #1f1a12; border: 2px solid #c79a2b; display: grid; place-items: center; color: #c79a2b; font-weight: bold; font-size: 36px; font-family: Georgia, serif;">{{ substr($author->name, 0, 1) }}</div>
        @endif
        <div>
            <span class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1.5px; color: #c79a2b; font-weight: bold;">{{ $author->designation ?: 'Editorial Contributor' }}</span>
            <h1 style="margin: 4px 0 10px 0; font-family: Georgia, serif; font-size: 32px;">{{ $author->name }}</h1>
            <p style="margin: 0; max-width: 650px; line-height: 1.6; opacity: 0.85;">{{ $author->bio ?: 'Editorial contributor covering business, markets, technology and culture.' }}</p>
            @if(!empty($author->social_links))
                <div style="display: flex; gap: 12px; margin-top: 14px; font-size: 14px;">
                    @foreach($author->social_links as $social)
                        @php
                            $socialParts = array_pad(explode('|', $social, 2), 2, '#');
                            $label = $socialParts[0];
                            $url = $socialParts[1];
                        @endphp
                        <a href="{{ $url }}" target="_blank" rel="noopener" style="color: #c79a2b; text-decoration: none; font-weight: bold;">{{ $label }} &rarr;</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<section class="news-shell content-grid" style="margin-top: 40px;">
    <div class="article-list">
        <h2 style="font-family: Georgia, serif; margin-bottom: 24px; border-bottom: 2px solid rgba(199,154,43,0.15); padding-bottom: 8px;">Articles by {{ $author->name }}</h2>
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
                <div>
                    <span>{{ optional($post->published_at)->format('M d, Y') }}</span>
                    <h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3>
                    <p>{{ $post->excerpt }}</p>
                </div>
            </article>
        @empty
            <div class="card empty-state">
                <h2>No articles found</h2>
                <p>Articles by {{ $author->name }} will appear here soon.</p>
            </div>
        @endforelse
        <div style="margin-top: 24px;">
            {{ $posts->links() }}
        </div>
    </div>
</section>

@endsection
