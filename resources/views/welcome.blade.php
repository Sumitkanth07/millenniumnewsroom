@extends('layouts.app')

@section('title', 'MILLENNIUM NEWSROOM - Premium Business News, Markets and Analysis')
@section('meta_description', 'A professional Laravel Blade news portal with markets, top headlines, technology, startups, opinion, videos and photo stories.')

@section('content')
    <section class="container homepage-grid">
        <div class="homepage-main">
            <section class="hero-news-section" aria-labelledby="lead-story-heading">
                <article class="lead-story">
                    <a class="lead-story__image" href="#">
                        <img src="{{ $leadStory['image'] }}" alt="{{ $leadStory['title'] }}">
                    </a>
                    <div class="lead-story__content">
                        <span class="section-kicker">{{ $leadStory['category'] }}</span>
                        <h1 id="lead-story-heading"><a href="#">{{ $leadStory['title'] }}</a></h1>
                        <p>{{ $leadStory['summary'] }}</p>
                        <div class="article-meta">
                            <span>By {{ $leadStory['author'] }}</span>
                            <span aria-hidden="true">|</span>
                            <span>{{ $leadStory['read_time'] }}</span>
                            <span aria-hidden="true">|</span>
                            <time datetime="{{ now()->toDateString() }}">{{ $leadStory['time'] }}</time>
                        </div>
                    </div>
                </article>

                <section class="top-headlines" aria-labelledby="top-headlines-heading">
                    <div class="section-heading">
                        <h2 id="top-headlines-heading">Top Headlines</h2>
                        <a href="#">View all</a>
                    </div>
                    <div class="top-headlines__grid">
                        @foreach ($topHeadlines as $headline)
                            <x-article-card :article="$headline" variant="headline" />
                        @endforeach
                    </div>
                </section>
            </section>

            <section class="content-band latest-news-feed" aria-labelledby="latest-news-heading">
                <div class="section-heading">
                    <h2 id="latest-news-heading">Latest News</h2>
                    <a href="#">Live updates</a>
                </div>
                <div class="latest-feed">
                    @foreach ($latestStories as $story)
                        <x-article-card :article="$story" variant="feed" :show-image="false" />
                    @endforeach
                </div>
            </section>

            <section class="content-band" aria-labelledby="editorial-picks-heading">
                <div class="section-heading">
                    <h2 id="editorial-picks-heading">Editorial Picks</h2>
                    <a href="#">Editor's desk</a>
                </div>
                <div class="article-grid article-grid--three">
                    @foreach ($editorialPicks as $story)
                        <x-article-card :article="$story" variant="feature" />
                    @endforeach
                </div>
            </section>

            <section class="content-band split-sections">
                <section aria-labelledby="technology-heading">
                    <div class="section-heading">
                        <h2 id="technology-heading">Technology</h2>
                        <a href="#">More tech</a>
                    </div>
                    <div class="article-stack">
                        @foreach ($technologyStories as $story)
                            <x-article-card :article="$story" variant="horizontal" />
                        @endforeach
                    </div>
                </section>

                <section aria-labelledby="startup-heading">
                    <div class="section-heading">
                        <h2 id="startup-heading">Startup</h2>
                        <a href="#">More startup</a>
                    </div>
                    <div class="article-stack">
                        @foreach ($startupStories as $story)
                            <x-article-card :article="$story" variant="horizontal" />
                        @endforeach
                    </div>
                </section>
            </section>

            <section class="content-band opinion-section" aria-labelledby="opinion-heading">
                <div class="section-heading">
                    <h2 id="opinion-heading">Opinion</h2>
                    <a href="#">All columns</a>
                </div>
                <div class="opinion-grid">
                    @foreach ($opinionStories as $story)
                        <x-article-card :article="$story" variant="opinion" :show-image="false" />
                    @endforeach
                </div>
            </section>

            <section class="content-band video-section" aria-labelledby="videos-heading">
                <div class="section-heading section-heading--dark">
                    <h2 id="videos-heading">Videos</h2>
                    <a href="#">Watch more</a>
                </div>
                <div class="video-grid">
                    @foreach ($videoStories as $video)
                        <article class="video-card">
                            <a class="video-card__media" href="#">
                                <img src="{{ $video['image'] }}" alt="{{ $video['title'] }}">
                                <span class="play-button" aria-hidden="true"></span>
                                <span class="video-duration">{{ $video['duration'] }}</span>
                            </a>
                            <div class="video-card__body">
                                <span class="section-kicker">{{ $video['category'] }}</span>
                                <h3><a href="#">{{ $video['title'] }}</a></h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="content-band photo-gallery-section" aria-labelledby="gallery-heading">
                <div class="section-heading">
                    <h2 id="gallery-heading">Photo Gallery</h2>
                    <a href="#">Open gallery</a>
                </div>
                <div class="photo-gallery">
                    @foreach ($photoGallery as $photo)
                        <figure class="gallery-card">
                            <a href="#">
                                <img src="{{ $photo['image'] }}" alt="{{ $photo['title'] }}">
                            </a>
                            <figcaption>{{ $photo['title'] }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>

            <section class="newsletter-block" aria-labelledby="newsletter-heading">
                <div>
                    <span class="section-kicker">Newsletter</span>
                    <h2 id="newsletter-heading">Get the market open briefing in your inbox</h2>
                    <p>Essential headlines, business signals and money insights before the trading day begins.</p>
                </div>
                <form class="newsletter-block__form" action="#">
                    <label class="sr-only" for="homepage-email">Email address</label>
                    <input id="homepage-email" type="email" placeholder="Email address">
                    <button type="submit">Subscribe</button>
                </form>
            </section>

            <section class="content-band infinite-news-section" aria-labelledby="more-news-heading">
                <div class="section-heading">
                    <h2 id="more-news-heading">More News</h2>
                    <a href="#">Personalize feed</a>
                </div>
                <div class="infinite-feed" aria-live="polite">
                    @foreach ($infiniteNewsListing as $story)
                        <x-article-card :article="$story" variant="feed" :show-image="false" />
                    @endforeach
                    <div class="feed-loader" role="status">
                        <span></span>
                        Loading more stories
                    </div>
                </div>
            </section>
        </div>

        <aside class="homepage-sidebar" aria-label="Trending and sidebar widgets">
            <section class="sidebar-widget sidebar-widget--sticky">
                <h2>Trending Stories</h2>
                <ol class="numbered-list numbered-list--large">
                    @foreach ($trendingStories as $story)
                        <li><a href="#">{{ $story }}</a></li>
                    @endforeach
                </ol>
            </section>

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
                        <ol class="numbered-list">
                            @foreach ($widget['items'] as $item)
                                <li><a href="#">{{ $item }}</a></li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            @endforeach

            <section class="sidebar-widget sidebar-widget--premium">
                <span class="section-kicker">Subscriber Only</span>
                <h2>Unlock deeper market intelligence</h2>
                <p>Access premium explainers, sharp opinion and company analysis curated for serious readers.</p>
                <a class="button-gold" href="#">Start reading</a>
            </section>
        </aside>
    </section>
@endsection
