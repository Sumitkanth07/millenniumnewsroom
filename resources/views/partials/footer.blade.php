@php
    $footerColumns = [
        'Categories' => ['News', 'Markets', 'Technology', 'Companies', 'Politics', 'Opinion', 'Sports', 'Lifestyle'],
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
            <form class="footer-newsletter__form" action="#">
                <label class="sr-only" for="footer-email">Email address</label>
                <input id="footer-email" type="email" placeholder="Email address">
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
                @foreach ($links as $link)
                    <a href="#">{{ $link }}</a>
                @endforeach
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
            @foreach ($policyLinks as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </nav>
    </div>
</footer>
