@extends('frontend.layout')

@section('content')

<article class="article article-layout">

    @php
        $shareUrl = urlencode($blog->publicUrl());
        $shareTitle = urlencode($blog->title);
        $rawUrl = $blog->publicUrl();
    @endphp

    <div class="sticky-share" style="display: flex; flex-direction: column; gap: 8px;">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="btn-share facebook" title="Share on Facebook" aria-label="Share on Facebook">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/></svg>
        </a>
        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="btn-share x-twitter" title="Share on X" aria-label="Share on X">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="btn-share linkedin" title="Share on LinkedIn" aria-label="Share on LinkedIn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93zM6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37z"/></svg>
        </a>
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="btn-share whatsapp" title="Share on WhatsApp" aria-label="Share on WhatsApp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.335 4.963L2 22l5.233-1.371a9.936 9.936 0 0 0 4.779 1.218h.004c5.506 0 9.989-4.478 9.99-9.986 0-2.668-1.039-5.176-2.927-7.064C17.192 3.008 14.685 2 12.012 2zm6.7 13.913c-.276.78-1.597 1.536-2.202 1.63-.53.08-1.224.1-3.626-.9-3.076-1.28-5.06-4.409-5.213-4.615-.154-.205-1.258-1.67-1.258-3.18 0-1.511.79-2.25 1.074-2.545.282-.295.62-.367.828-.367.207 0 .415.004.595.012.185.008.436-.076.683.518.252.606.862 2.1.938 2.255.075.155.125.337.021.545-.104.207-.156.337-.31.518-.156.182-.328.406-.468.545-.156.155-.32.325-.137.637.183.31.815 1.346 1.747 2.176.93.83 1.713 1.087 2.025 1.244.31.155.49.13.676-.086.185-.216.79-.92.998-1.235.208-.316.416-.264.7-.155.285.11 1.807.852 2.118 1.008.31.155.518.232.595.362.077.13.077.754-.2 1.534z"/></svg>
        </a>
        <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="btn-share telegram" title="Share on Telegram" aria-label="Share on Telegram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-1-.65-.35-1 .22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 0 0-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.94-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.37.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .24z"/></svg>
        </a>
        <a href="mailto:?subject={{ $shareTitle }}&body={{ $shareUrl }}" class="btn-share email" title="Share via Email" aria-label="Share via Email">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        </a>
        <button onclick="copyToClipboard(event, '{{ $rawUrl }}')" class="btn-share copy" title="Copy Link" aria-label="Copy Link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
        </button>
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
            By @if($blog->author)<a href="{{ route('author.show', $blog->author->slug) }}" style="color: #c79a2b; text-decoration: none; font-weight: bold;">{{ $blog->author->name }}</a>@else MILLENNIUM NEWSROOM Desk @endif
            &middot;
            Published: {{ optional($blog->published_at)->format('M d, Y h:i A') }}
            @if($blog->updated_at && $blog->updated_at->gt($blog->published_at->addMinutes(5)))
                &middot; Updated: {{ $blog->updated_at->format('M d, Y h:i A') }}
            @endif
        </p>

        @if($blog->author)
            <div class="author-bio author-card">

                @if($blog->author->image)
                    <img 
                        src="{{ asset($blog->author->image) }}"
                        alt="{{ $blog->author->name }}"
                        width="50"
                        height="50"
                        loading="lazy">
                @endif

                <div>
                    <strong><a href="{{ route('author.show', $blog->author->slug) }}" style="color: #c79a2b; text-decoration: none;">{{ $blog->author->name }}</a></strong>
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
                    width="1200"
                    height="675"
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
        <div class="share-row" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px;">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="btn-share facebook" title="Share on Facebook" aria-label="Share on Facebook">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/></svg>
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="btn-share x-twitter" title="Share on X" aria-label="Share on X">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="btn-share linkedin" title="Share on LinkedIn" aria-label="Share on LinkedIn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93zM6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37z"/></svg>
            </a>
            <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="btn-share whatsapp" title="Share on WhatsApp" aria-label="Share on WhatsApp">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.335 4.963L2 22l5.233-1.371a9.936 9.936 0 0 0 4.779 1.218h.004c5.506 0 9.989-4.478 9.99-9.986 0-2.668-1.039-5.176-2.927-7.064C17.192 3.008 14.685 2 12.012 2zm6.7 13.913c-.276.78-1.597 1.536-2.202 1.63-.53.08-1.224.1-3.626-.9-3.076-1.28-5.06-4.409-5.213-4.615-.154-.205-1.258-1.67-1.258-3.18 0-1.511.79-2.25 1.074-2.545.282-.295.62-.367.828-.367.207 0 .415.004.595.012.185.008.436-.076.683.518.252.606.862 2.1.938 2.255.075.155.125.337.021.545-.104.207-.156.337-.31.518-.156.182-.328.406-.468.545-.156.155-.32.325-.137.637.183.31.815 1.346 1.747 2.176.93.83 1.713 1.087 2.025 1.244.31.155.49.13.676-.086.185-.216.79-.92.998-1.235.208-.316.416-.264.7-.155.285.11 1.807.852 2.118 1.008.31.155.518.232.595.362.077.13.077.754-.2 1.534z"/></svg>
            </a>
            <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="btn-share telegram" title="Share on Telegram" aria-label="Share on Telegram">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-1-.65-.35-1 .22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 0 0-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.94-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.37.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .24z"/></svg>
            </a>
            <a href="mailto:?subject={{ $shareTitle }}&body={{ $shareUrl }}" class="btn-share email" title="Share via Email" aria-label="Share via Email">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            </a>
            <button onclick="copyToClipboard(event, '{{ $rawUrl }}')" class="btn-share copy" title="Copy Link" aria-label="Copy Link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
        </div>

        <aside class="toc">
            <strong>Table of contents</strong>
            <a href="#story">Story</a>
            <a href="#related">Related stories</a>
        </aside>

        <x-ad-slot placement="single_post_top" label="Single Post Top Ad" />

        <div id="story" class="content">
            @php
                $paragraphs = explode('</p>', $blog->content);
            @endphp
            @foreach($paragraphs as $index => $paragraph)
                @if(trim($paragraph) !== '')
                    {!! $paragraph !!}</p>
                @endif
                
                @if($index === 2)
                    <x-ad-slot placement="after_3rd_paragraph" label="After 3rd Paragraph Ad" />
                @endif
                @if($index === 4)
                    <x-ad-slot placement="after_5th_paragraph" label="After 5th Paragraph Ad" />
                @endif
                @if($index === 6)
                    <x-ad-slot placement="after_7th_paragraph" label="After 7th Paragraph Ad" />
                @endif
            @endforeach
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

        <x-ad-slot placement="before_related_posts" label="Before Related Posts Ad" />

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

        <x-ad-slot placement="after_related_posts" label="After Related Posts Ad" />

    </div>

    <aside class="sidebar">

        <x-ad-slot placement="sidebar_top" label="Sidebar Top Ad" />

        <h2>Trending</h2>

        @foreach($trendingPosts as $post)

            <a class="trend" href="{{ $post->publicUrl() }}">

                <strong>{{ $loop->iteration }}</strong>

                <span>{{ $post->title }}</span>

            </a>

        @endforeach

        <x-ad-slot placement="sidebar_middle" label="Sidebar Middle Ad" />

        <x-ad-slot placement="sidebar_bottom" label="Sidebar Bottom Sticky Ad" :sticky="true" />

    </aside>

<script>
function copyToClipboard(event, url) {
    event.preventDefault();
    navigator.clipboard.writeText(url).then(() => {
        const btn = event.currentTarget;
        const originalSVG = btn.innerHTML;
        btn.innerHTML = 'Copied!';
        btn.style.fontSize = '10px';
        setTimeout(() => {
            btn.innerHTML = originalSVG;
            btn.style.fontSize = '';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}
</script>
@endsection
