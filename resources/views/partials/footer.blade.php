@php
    $dynamicCategories = \Illuminate\Support\Facades\Cache::remember('footer.categories.partial', 90, function() {
        return \App\Models\Category::where('is_active', true)
            ->whereHas('blogs', fn($q) => $q->published())
            ->withCount(['blogs' => fn($q) => $q->published()])
            ->orderByDesc('blogs_count')
            ->take(10)
            ->get();
    });

    $footerColumns = [
        'Categories' => $dynamicCategories,
        'Trending Topics' => ['Stock Market', 'Income Tax', 'Mutual Funds', 'IPO Watch', 'Gold Rates', 'AI News', 'Startup Funding'],
        'About Us' => ['About MILLENNIUM NEWSROOM', 'Advertise With Us', 'Careers', 'Press Releases', 'Investor Relations'],
        'Editorial Team' => ['Editor-in-Chief', 'Business Desk', 'Markets Desk', 'Opinion Editors', 'Corrections Policy'],
        'Contact' => ['Contact Us', 'Customer Support', 'News Tips', 'Partnerships', 'RSS Feeds'],
    ];

    $policyLinks = ['Privacy Policy', 'Terms', 'Sitemap', 'Cookie Policy', 'Code of Conduct'];
@endphp

<footer class="site-footer" aria-labelledby="footer-heading">
    <div class="container footer-newsletter">
        <div class="footer-newsletter__copy">
            <span class="section-kicker">Premium Briefing</span>
            <h2 id="footer-heading">Business news with context, delivered every morning.</h2>
            <p>Join readers who rely on MILLENNIUM NEWSROOM for markets, policy, companies, technology and personal finance coverage.</p>
        </div>

        <div class="footer-newsletter__panel">
            @if(session('newsletter_status'))
                <div style="background-color: #e6f4ea; color: #137333; padding: 10px 14px; border-radius: 4px; font-size: 13px; font-weight: 600; margin-bottom: 10px;">
                    {{ session('newsletter_status') }}
                </div>
            @endif
            <form class="footer-newsletter__form" method="POST" action="{{ route('newsletter.subscribe') }}">
                @csrf
                <label class="sr-only" for="footer-email">Email address</label>
                <input id="footer-email" name="email" type="email" placeholder="Email address" required>
                <button type="submit">Subscribe</button>
            </form>

            <div class="social-links" aria-label="Social links">
                <a href="#" aria-label="Follow MILLENNIUM NEWSROOM on X">
                    <span>X</span>
                </a>
                <a href="#" aria-label="Follow MILLENNIUM NEWSROOM on Facebook">
                    <span>f</span>
                </a>
                <a href="#" aria-label="Follow MILLENNIUM NEWSROOM on LinkedIn">
                    <span>in</span>
                </a>
                <a href="#" aria-label="Watch MILLENNIUM NEWSROOM on YouTube">
                    <span>yt</span>
                </a>
                <a href="#" aria-label="Follow MILLENNIUM NEWSROOM on Instagram">
                    <span>ig</span>
                </a>
            </div>
        </div>
    </div>

    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="brand-logo brand-logo--footer" href="{{ url('/') }}">MILLENNIUM<span>NEWSROOM</span></a>
            <p>Independent business journalism for markets, companies, policy, money and modern India.</p>
            <address>
                MILLENNIUM NEWSROOM News Network<br>
                14 Editorial House, Business District<br>
                New Delhi, India<br>
                <a href="mailto:newsroom@millenniumnewsroom.test">newsroom@millenniumnewsroom.test</a>
            </address>
        </div>

        @foreach ($footerColumns as $title => $links)
            <nav class="footer-column" aria-label="{{ $title }}">
                <h3>{{ $title }}</h3>
                @if($title === 'Categories')
                    @foreach ($links->take(10) as $link)
                        <a href="{{ route('category.show', $link->slug) }}">{{ $link->name }}</a>
                    @endforeach
                    <a href="{{ route('categories.index') }}" style="color: #c79a2b; font-weight: bold; margin-top: 6px; display: inline-block;">View All Categories &rarr;</a>
                @else
                    @foreach ($links as $link)
                        <a href="#">{{ $link }}</a>
                    @endforeach
                @endif
            </nav>
        @endforeach
    </div>

    <div class="container footer-contact">
        <a href="#">
            <strong>Editorial Standards</strong>
            <span>Read how our newsroom verifies facts and corrects errors.</span>
        </a>
        <a href="#">
            <strong>Contact the Newsroom</strong>
            <span>Send tips, documents, corrections or story ideas securely.</span>
        </a>
        <a href="#">
            <strong>Advertise</strong>
            <span>Reach an engaged audience of business and policy readers.</span>
        </a>
    </div>

    <div class="container footer-bottom">
        <span>&copy; {{ now()->year }} MILLENNIUM NEWSROOM News Network. All rights reserved.</span>
        <nav class="footer-policy" aria-label="Legal links">
            <a href="{{ route('page.show', 'privacy-policy') }}">Privacy Policy</a>
            <a href="{{ route('page.show', 'terms') }}">Terms</a>
            <a href="{{ route('sitemap.page') }}">Sitemap</a>
            <a href="{{ route('page.show', 'cookie-policy') }}">Cookie Policy</a>
            <a href="javascript:void(0)" onclick="openCookieConsentSettings()" style="cursor: pointer;">Cookie Preferences</a>
        </nav>
    </div>
</footer>
