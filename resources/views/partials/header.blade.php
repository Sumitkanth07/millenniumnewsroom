@php
    $navigation = $navItems ?? config('navigation.items', ['News', 'Markets', 'Technology', 'Companies', 'Politics', 'Opinion', 'Sports', 'Lifestyle']);
    $tickerItems = $breakingNews ?? config('navigation.breaking', [
        'Markets open higher as banks and energy shares advance',
        'Gold prices hold steady ahead of global rate signals',
        'Premium subscribers get early access to weekend long reads',
    ]);
    $menuSections = $megaMenuSections ?? config('navigation.mega', []);
@endphp

<header class="site-header">
    <input class="nav-toggle" type="checkbox" id="mobile-nav-toggle" aria-label="Open navigation menu">

    <div class="utility-bar">
        <div class="container utility-bar__inner">
            <div class="utility-links" aria-label="Utility links">
                <a href="#">Newsletter</a>
            </div>

            <form class="utility-search" action="#" role="search">
                <label class="sr-only" for="site-search">Search news</label>
                <input id="site-search" type="search" placeholder="Search news, markets, companies">
                <button type="submit">Search</button>
            </form>

            <div class="account-links">
                <a class="login-link" href="#">Login</a>
                <a class="premium-link" href="#">Premium</a>
            </div>
        </div>
    </div>

    <div class="brand-bar">
        <div class="container brand-bar__inner">
            <a class="brand-logo" href="{{ url('/') }}" aria-label="MILLENNIUM NEWSROOM home">
                MILLENNIUM<span>NEWSROOM</span>
            </a>
            <div class="brand-meta">
                <span>{{ now()->format('l, d F Y') }}</span>
                <span>India edition</span>
            </div>
            <label class="hamburger-button" for="mobile-nav-toggle" aria-label="Toggle navigation menu">
                <span></span>
                <span></span>
                <span></span>
            </label>
        </div>
    </div>

    <div class="breaking-ticker" aria-label="Breaking news">
        <div class="container breaking-ticker__inner">
            <span class="ticker-label">Breaking</span>
            <div class="ticker-window">
                <div class="ticker-track">
                    @foreach ($tickerItems as $item)
                        <a href="#">{{ $item }}</a>
                    @endforeach
                    @foreach ($tickerItems as $item)
                        <a href="#">{{ $item }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <nav class="main-nav" aria-label="Main navigation">
        <div class="container main-nav__inner">
            <ul class="nav-list nav-list--desktop">
                @foreach ($navigation as $item)
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ $item }}</a>

                        @if (isset($menuSections[$item]))
                            <div class="mega-menu" aria-label="{{ $item }} menu">
                                <div class="mega-menu__intro">
                                    <strong>{{ $item }}</strong>
                                    <span>Newspaper-style coverage, sharp analysis and curated reads.</span>
                                </div>

                                <div class="mega-menu__columns">
                                    @foreach ($menuSections[$item]['columns'] ?? [] as $column)
                                        <section class="mega-column">
                                            <h3>{{ $column['heading'] }}</h3>
                                            @foreach ($column['links'] as $link)
                                                <a href="#">{{ $link }}</a>
                                            @endforeach
                                        </section>
                                    @endforeach
                                </div>

                                <div class="mega-menu__featured">
                                    @foreach ($menuSections[$item]['featured'] ?? [] as $post)
                                        <a class="mega-featured-card" href="#">
                                            <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}">
                                            <span class="section-kicker">{{ $post['category'] }}</span>
                                            <strong>{{ $post['title'] }}</strong>
                                            <small>{{ $post['summary'] }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mobile-menu" aria-label="Mobile navigation">
                <div class="mobile-menu__top">
                    <a href="#">Login</a>
                </div>

                @foreach ($navigation as $item)
                    <details class="mobile-menu__group">
                        <summary>{{ $item }}</summary>
                        @if (isset($menuSections[$item]))
                            <div class="mobile-menu__columns">
                                @foreach ($menuSections[$item]['columns'] ?? [] as $column)
                                    <section>
                                        <h3>{{ $column['heading'] }}</h3>
                                        @foreach ($column['links'] as $link)
                                            <a href="#">{{ $link }}</a>
                                        @endforeach
                                    </section>
                                @endforeach
                            </div>
                            <div class="mobile-featured">
                                @foreach ($menuSections[$item]['featured'] ?? [] as $post)
                                    <a href="#">
                                        <span class="section-kicker">{{ $post['category'] }}</span>
                                        <strong>{{ $post['title'] }}</strong>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </details>
                @endforeach
            </div>
        </div>
    </nav>
</header>
