@extends('layouts.app')

@section('title', $article['seo_title'])
@section('meta_description', $article['seo_description'])
@section('body_class', 'news-portal article-page-body')

@push('head')
    <link rel="canonical" href="{{ $article['canonical'] }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $article['headline'] }}">
    <meta property="og:description" content="{{ $article['seo_description'] }}">
    <meta property="og:image" content="{{ $article['image'] }}">
    <meta property="article:published_time" content="{{ $article['published_at'] }}">
    <meta property="article:modified_time" content="{{ $article['updated_at'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article['headline'],
            'description' => $article['seo_description'],
            'image' => [$article['image']],
            'datePublished' => $article['published_at'],
            'dateModified' => $article['updated_at'],
            'author' => [
                '@type' => 'Person',
                'name' => $article['author']['name'],
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'MILLENNIUM NEWSROOM',
            ],
            'mainEntityOfPage' => $article['canonical'],
        ], JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <article class="container article-layout" itemscope itemtype="https://schema.org/NewsArticle">
        <div class="article-primary">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span aria-hidden="true">/</span>
                <a href="#">{{ $article['category'] }}</a>
                <span aria-hidden="true">/</span>
                <span>Market close</span>
            </nav>

            <header class="article-header">
                <span class="section-kicker" itemprop="articleSection">{{ $article['category'] }}</span>
                <h1 itemprop="headline">{{ $article['headline'] }}</h1>
                <p class="article-dek" itemprop="description">{{ $article['dek'] }}</p>

                <div class="article-meta-bar">
                    <div class="author-card" itemprop="author" itemscope itemtype="https://schema.org/Person">
                        <span class="author-avatar" aria-hidden="true">{{ $article['author']['avatar'] }}</span>
                        <div>
                            <strong itemprop="name">{{ $article['author']['name'] }}</strong>
                            <span>{{ $article['author']['role'] }}</span>
                        </div>
                    </div>

                    <div class="publish-meta">
                        <span>{{ $article['read_time'] }}</span>
                        <span>Published <time datetime="{{ $article['published_at'] }}">{{ \Carbon\Carbon::parse($article['published_at'])->format('d M Y, h:i A') }} IST</time></span>
                        <span>Updated <time datetime="{{ $article['updated_at'] }}">{{ \Carbon\Carbon::parse($article['updated_at'])->format('h:i A') }} IST</time></span>
                    </div>
                </div>

                <div class="share-row" aria-label="Share this article">
                    <span>Share</span>
                    <a href="#" aria-label="Share on X">X</a>
                    <a href="#" aria-label="Share on Facebook">f</a>
                    <a href="#" aria-label="Share on LinkedIn">in</a>
                    <a href="#" aria-label="Share on WhatsApp">wa</a>
                    <a href="#" aria-label="Share by email">mail</a>
                </div>

                <figure class="article-hero-image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                    <img src="{{ $article['image'] }}" alt="{{ $article['headline'] }}">
                    <figcaption>{{ $article['image_caption'] }}</figcaption>
                </figure>
            </header>

            <div class="article-body-layout">
                <aside class="table-of-contents" aria-labelledby="toc-heading">
                    <h2 id="toc-heading">Table of Contents</h2>
                    @foreach ($toc as $item)
                        <a href="#{{ $item['id'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </aside>

                <div class="article-content" itemprop="articleBody">
                    <section class="author-bio-box" aria-label="Author bio">
                        <strong>About the author</strong>
                        <p>{{ $article['author']['bio'] }}</p>
                    </section>

                    <div class="inline-ad" aria-label="Advertisement">
                        <span>Advertisement</span>
                    </div>

                    @foreach ($contentSections as $section)
                        <section id="{{ $section['id'] }}" class="article-section">
                            <h2>{{ $section['heading'] }}</h2>
                            @foreach ($section['paragraphs'] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </section>

                        @if ($loop->iteration === 2)
                            <div class="inline-ad inline-ad--wide" aria-label="Advertisement">
                                <span>Advertisement</span>
                            </div>
                        @endif
                    @endforeach

                    <section class="tags-block" aria-label="Article tags">
                        <h2>Tags</h2>
                        <div class="tag-list">
                            @foreach ($tags as $tag)
                                <a href="#">{{ $tag }}</a>
                            @endforeach
                        </div>
                    </section>

                    <section class="related-stories" aria-labelledby="related-heading">
                        <div class="section-heading">
                            <h2 id="related-heading">Related Stories</h2>
                            <a href="#">More markets</a>
                        </div>
                        <div class="article-grid article-grid--three">
                            @foreach ($relatedStories as $story)
                                <x-article-card :article="$story" variant="feature" :show-image="false" />
                            @endforeach
                        </div>
                    </section>

                </div>
            </div>
        </div>

        <aside class="article-sidebar" aria-label="Right sidebar">
            <section class="sidebar-widget sidebar-widget--sticky">
                <h2>Trending News</h2>
                <ol class="numbered-list numbered-list--large">
                    @foreach ($trendingNews as $item)
                        <li><a href="#">{{ $item }}</a></li>
                    @endforeach
                </ol>
            </section>

            <section class="sidebar-widget sidebar-widget--premium">
                <span class="section-kicker">Premium</span>
                <h2>Unlock market insight</h2>
                <p>Get deeper sector analysis, earnings explainers and portfolio ideas from the MILLENNIUM NEWSROOM newsroom.</p>
                <a class="button-gold" href="#">Subscribe now</a>
            </section>

            <div class="sidebar-ad" aria-label="Advertisement">
                <span>Advertisement</span>
            </div>
        </aside>
    </article>
@endsection
