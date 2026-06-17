@extends('frontend.layout')

@section('content')

<article class="article article-layout">

    <div class="sticky-share">
        <button class="btn small">Share</button>
        <button class="btn small">X</button>
        <button class="btn small">In</button>
    </div>

    <div>

        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>

            /

            @if($blog->category)
                <a href="{{ route('category.show', $blog->category) }}">
                    {{ $blog->category->name }}
                </a>
                /
            @endif

            <span>{{ $blog->title }}</span>
        </nav>

        <p class="eyebrow">
            {{ $blog->category?->name ?? 'News' }}
        </p>

        <h1>{{ $blog->title }}</h1>

        <p class="muted">
            By {{ $blog->author?->name ?? 'MILLENNIUM NEWSROOM Desk' }}
            Â·
            {{ optional($blog->published_at)->format('M d, Y h:i A') }}
        </p>

        @if($blog->author)
            <div class="author-bio author-card">

                @if($blog->author->image)
                    <img 
                        src="{{ asset($blog->author->image) }}"
                        alt="{{ $blog->author->name }}"
                        loading="lazy">
                @endif

                <div>
                    <strong>{{ $blog->author->name }}</strong>
                    <p>{{ $blog->author->bio }}</p>
                </div>

            </div>
        @endif

        @if($blog->featured_image || $blog->image)
            @php
                $showImg = $blog->featured_image ?: $blog->image;
                if ($showImg && !str_starts_with($showImg, 'http') && !str_starts_with($showImg, '/')) {
                    $showImg = ltrim($showImg, '/');
                }
            @endphp
            <figure>

                <img 
                    class="article-image"
                    src="{{ asset($showImg) }}"
                    alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                    title="{{ $blog->featured_image_title ?: $blog->title }}"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async">

                @if($blog->featured_image_caption)
                    <figcaption>
                        {{ $blog->featured_image_caption }}
                    </figcaption>
                @endif

            </figure>

        @endif

        <p class="muted">
            {{ $blog->reading_time }} min read
        </p>
        <div class="share-row">
            <button class="btn small">Share</button>
            <button class="btn small">X</button>
            <button class="btn small">LinkedIn</button>
        </div>

        <aside class="toc">
            <strong>Table of contents</strong>
            <a href="#story">Story</a>
            <a href="#related">Related stories</a>
        </aside>

        <x-ad-slot :ads="$ads" placement="in_content_ad" label="In-content ad" />

        <div id="story" class="content">
            {!! $blog->content !!}
        </div>

        @if($blog->gallery_images)

            <section class="article-gallery">

                <div class="section-head">
                    <div>
                        <p class="eyebrow">Gallery</p>
                        <h2>Photo Gallery</h2>
                    </div>
                </div>

                <div class="gallery-grid">

                    @foreach($blog->gallery_images as $image)

                        <figure>

                            <img 
                                src="{{ asset($image) }}"
                                    alt="{{ $blog->title }} gallery image {{ $loop->iteration }}"
                                    loading="lazy"
                            >

                        </figure>

                    @endforeach

                </div>

            </section>

        @endif

        <x-ad-slot :ads="$ads" placement="footer_ad" label="Footer ad" />

        <div class="tags">
            @foreach($blog->tags as $tag)
                <span>{{ $tag->name }}</span>
            @endforeach
        </div>

        <section id="related">

            <h2>Related Stories</h2>

            <div class="card-grid">

                @foreach($relatedPosts as $post)

                    <article class="card">

                        <h3>
                            <a href="{{ $post->publicUrl() }}">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <p>{{ $post->excerpt }}</p>

                    </article>

                @endforeach

            </div>

        </section>

    </div>

    <aside class="sidebar">

        <h2>Trending</h2>

        @foreach($trendingPosts as $post)

            <a class="trend" href="{{ $post->publicUrl() }}">

                <strong>{{ $loop->iteration }}</strong>

                <span>{{ $post->title }}</span>

            </a>

        @endforeach

    </aside>

</article>

<script type="application/ld+json">
{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@endsection
