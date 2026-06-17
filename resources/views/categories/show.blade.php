@extends('layouts.app')

@section('title', $category['name'].' News - Latest Market Updates and Analysis | MILLENNIUM NEWSROOM')
@section('meta_description', $category['description'])
@section('body_class', 'news-portal category-page-body')

@push('head')
    <link rel="canonical" href="{{ route('categories.markets') }}">
@endpush

@section('content')
    <section class="category-hero" aria-labelledby="category-heading">
        <div class="container category-hero__inner">
            <div>
                <nav class="breadcrumb breadcrumb--light" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}">Home</a>
                    <span aria-hidden="true">/</span>
                    <span>{{ $category['name'] }}</span>
                </nav>
                <span class="section-kicker">{{ $category['kicker'] }}</span>
                <h1 id="category-heading">{{ $category['title'] }}</h1>
                <p>{{ $category['description'] }}</p>
            </div>

            <div class="category-stats" aria-label="Category statistics">
                @foreach ($category['stats'] as $stat)
                    <div>
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container category-layout">
        <div class="category-main">
            <section class="category-featured" aria-labelledby="featured-category-heading">
                <div class="section-heading">
                    <h2 id="featured-category-heading">Featured Story</h2>
                    <a href="#">Editor’s pick</a>
                </div>
                <article class="category-featured-card">
                    <a class="category-featured-card__image" href="{{ route('articles.show') }}">
                        <img src="{{ $featuredStory['image'] }}" alt="{{ $featuredStory['title'] }}">
                    </a>
                    <div>
                        <span class="section-kicker">{{ $featuredStory['category'] }}</span>
                        <h3><a href="{{ route('articles.show') }}">{{ $featuredStory['title'] }}</a></h3>
                        <p>{{ $featuredStory['summary'] }}</p>
                        <div class="article-meta">
                            <span>{{ $featuredStory['read_time'] }}</span>
                            <span aria-hidden="true">|</span>
                            <span>{{ $featuredStory['time'] }}</span>
                        </div>
                    </div>
                </article>
            </section>

            <section class="category-listing" aria-labelledby="category-listing-heading">
                <div class="category-filter-bar">
                    <div>
                        <span class="section-kicker">All Stories</span>
                        <h2 id="category-listing-heading">Latest {{ $category['name'] }} News</h2>
                    </div>
                    <div class="filter-tabs" aria-label="Filter articles">
                        <a class="{{ $activeFilter === 'latest' ? 'is-active' : '' }}" href="{{ route('categories.markets', ['filter' => 'latest']) }}">Latest</a>
                        <a class="{{ $activeFilter === 'popular' ? 'is-active' : '' }}" href="{{ route('categories.markets', ['filter' => 'popular']) }}">Popular</a>
                    </div>
                </div>

                <div class="category-feed">
                    @foreach ($articles as $article)
                        <x-article-card :article="$article" variant="horizontal" />
                    @endforeach
                </div>

                <nav class="pagination-nav" aria-label="Pagination">
                    <a class="pagination-nav__control is-disabled" href="#" aria-disabled="true">Previous</a>
                    <div>
                        @foreach ($pagination['pages'] as $page)
                            <a class="{{ $page === $pagination['current'] ? 'is-active' : '' }}" href="#">{{ $page }}</a>
                        @endforeach
                    </div>
                    <a class="pagination-nav__control" href="{{ $pagination['next'] }}">Next</a>
                </nav>
            </section>
        </div>

        <aside class="category-sidebar" aria-label="Category sidebar">
            @foreach ($sidebarWidgets as $widget)
                <section class="sidebar-widget">
                    <h2>{{ $widget['title'] }}</h2>

                    @if ($widget['type'] === 'market-movers')
                        <div class="mover-list">
                            @foreach ($widget['items'] as $item)
                                <a href="#">
                                    <span>{{ $item['name'] }}</span>
                                    <strong class="{{ $item['positive'] ? 'text-positive' : 'text-negative' }}">{{ $item['value'] }}</strong>
                                </a>
                            @endforeach
                        </div>
                    @elseif ($widget['type'] === 'numbered-list')
                        <ol class="numbered-list numbered-list--large">
                            @foreach ($widget['items'] as $item)
                                <li><a href="#">{{ $item }}</a></li>
                            @endforeach
                        </ol>
                    @elseif ($widget['type'] === 'link-list')
                        <div class="tag-list tag-list--compact">
                            @foreach ($widget['items'] as $item)
                                <a href="#">{{ $item }}</a>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach

            <section class="sidebar-widget sidebar-widget--premium">
                <span class="section-kicker">Premium</span>
                <h2>Deeper market intelligence</h2>
                <p>Get analyst-backed explainers, sector insights and portfolio ideas from the MILLENNIUM NEWSROOM newsroom.</p>
                <a class="button-gold" href="#">Subscribe now</a>
            </section>
        </aside>
    </section>
@endsection
