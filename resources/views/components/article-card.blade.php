@props([
    'article' => [],
    'variant' => 'standard',
    'showImage' => true,
    'rank' => null,
])

@php

    $category =
        $article->category->name
        ?? 'News';

    $title =
        $article->title
        ?? 'Untitled story';

    $summary =
        $article->excerpt
        ?? null;

    $image =
        $article->featured_image
        ?: $article->image;

    $slug =
        $article->slug
        ?? null;

    $url =
        method_exists($article, 'publicUrl')
        ? $article->publicUrl()
        : url('/'.str($category)->slug().'/'.$slug);

    $readTime =
        $article->reading_time
        ?? null;

    $time =
        optional($article->published_at)?->diffForHumans();

    $isPremium =
        strtolower($category) === 'premium';

@endphp

<article {{ $attributes->merge([
    'class' => 'article-card article-card--'.$variant
]) }}>

    @if ($rank)

        <span class="article-card__rank">
            {{ $rank }}
        </span>

    @endif

    @if ($showImage && $image)

        <a
            class="article-card__image"
            href="{{ $url }}"
        >

            <img
                src="{{ asset(ltrim($image, '/')) }}"
                alt="{{ $title }}"
                loading="lazy"
            >

        </a>

    @endif

    <div class="article-card__body">

        <div class="article-card__meta-row">

            <span class="section-kicker">
                {{ $category }}
            </span>

            @if ($isPremium)

                <span class="premium-badge">
                    Premium
                </span>

            @endif

        </div>

        <h3>

            <a href="{{ $url }}">

                {{ $title }}

            </a>

        </h3>

        @if ($summary && $variant !== 'compact')

            <p>{{ $summary }}</p>

        @endif

        @if ($readTime || $time)

            <div class="article-meta">

                @if ($readTime)

                    <span>{{ $readTime }} min read</span>

                @endif

                @if ($readTime && $time)

                    <span aria-hidden="true">|</span>

                @endif

                @if ($time)

                    <span>{{ $time }}</span>

                @endif

            </div>

        @endif

    </div>

</article>
