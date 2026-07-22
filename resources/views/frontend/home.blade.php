@extends('frontend.layout')

@section('content')
<section class="portal-home">
    @php
        $sliderPosts = $featuredPosts->concat($topHeadlines)->concat($latestBlogs)->unique('id')->take(5);
        $sectionEnabled = fn (string $key) => $homepageSections->get($key)?->is_active ?? true;
    @endphp

    @if($sectionEnabled('hero') && $sliderPosts->isNotEmpty())
    <div class="hero-slider" data-slider>
        @foreach($sliderPosts->take(5) as $post)
            @php
                $heroImage = $post->featured_image ?: $post->image;
                if ($heroImage && !str_starts_with($heroImage, 'http') && !str_starts_with($heroImage, '/')) {
                    $heroImage = ltrim($heroImage, '/');
                }
            @endphp
            <article class="hero-slide @if($loop->first) active @endif" data-article-url="{{ $post->publicUrl() }}">
                @if($heroImage)
                    @if($loop->first)
                        @push('head')
                            <link rel="preload" as="image" href="{{ asset($heroImage) }}">
                        @endpush
                    @endif
                    <img 
                        src="{{ asset($heroImage) }}"
                        alt="{{ $post->featured_image_alt ?: $post->title }}"
                        width="1200"
                        height="500"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                        fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                        decoding="async"
                    >
                @else
                    <div class="hero-slide-placeholder" style="position: absolute; inset: 0; background: #14100a; display: grid; place-items: center;"><span style="font-family: Georgia, serif; font-size: 54px; color: #f5da80;">{{ strtoupper(substr($post->category?->name ?? 'MN', 0, 2)) }}</span></div>
                @endif
                <div class="hero-slide-copy">
                    <span class="badge gold">{{ $post->category?->name ?? 'Featured' }}</span>
                    @if($loop->first)
                        <h1><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h1>
                    @else
                        <h2 class="hero-title"><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h2>
                    @endif
                    <p>{{ \Illuminate\Support\Str::limit($post->excerpt, 140) }}</p>
                    <div class="banner-meta" style="margin-top: 10px;">
                        <small>{{ optional($post->published_at)->format('M d, Y') }}</small>
                        <small style="color: #efe5d1;">• {{ $post->reading_time }} min read</small>
                    </div>
                </div>
            </article>
        @endforeach

        <button class="slider-btn prev" type="button" data-slide-prev aria-label="Previous story">&lsaquo;</button>
        <button class="slider-btn next" type="button" data-slide-next aria-label="Next story">&rsaquo;</button>
    </div>
    @endif

    <div class="news-shell home-shell" style="padding-top: 10px;">
        <x-ad-slot :ads="$ads" placement="header_ad" label="Header responsive ad" />

        <!-- Breaking News / Top Stories & Fresh News -->
        <section class="spotlight-grid reveal" style="align-items: stretch;">
            <div class="top-headlines-panel" style="display: flex; flex-direction: column;">
                <div class="section-head" style="margin-bottom: 24px;">
                    <div>
                        <p class="eyebrow">Top Stories</p>
                        <h2 style="font-size: 28px; font-family: Georgia, serif;">Fresh News Feed</h2>
                    </div>
                    <a href="{{ route('search') }}" style="color: #c79a2b; font-weight: bold;">View all</a>
                </div>
                
                <div class="headline-mosaic" style="flex: 1;">
                    @foreach($latestBlogs->take(6) as $post)
                        @php
                            $topImage = $post->featured_image ?: $post->image;
                            if ($topImage && !str_starts_with($topImage, 'http') && !str_starts_with($topImage, '/')) {
                                $topImage = ltrim($topImage, '/');
                            }
                        @endphp
                        <article class="mosaic-card @if($loop->first) large @endif">
                            @if($topImage)
                                <img src="{{ $loop->first ? asset($topImage) : $post->getThumbnailUrl() }}" alt="{{ $post->title }}" width="800" height="450" loading="lazy" decoding="async">
                            @endif
                            <div style="background: linear-gradient(transparent, rgba(0,0,0,0.9));">
                                <span style="background: #c79a2b; color: #1f1a12; padding: 3px 8px; border-radius: 4px; font-size: 10px;">{{ $post->category?->name }}</span>
                                <h3 style="margin-top: 10px;"><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <!-- Trending Sidebar -->
            <aside class="sidebar active-sidebar" style="background: rgba(255,255,255,0.6); padding: 24px; border-radius: 12px; border: 1px solid rgba(199,154,43,0.2);">
                <h2 style="font-size: 22px; font-family: Georgia, serif; border-bottom: 2px solid #c79a2b; padding-bottom: 10px; margin-bottom: 20px;">Trending Now</h2>
                @foreach($mostRead->take(5) as $post)
                    <a class="trend" href="{{ $post->publicUrl() }}" style="border-bottom: 1px solid rgba(199,154,43,0.1); padding: 14px 0;">
                        <strong style="font-size: 32px; opacity: 0.8;">{{ $loop->iteration }}</strong>
                        <span style="font-size: 16px; line-height: 1.4;">
                            {{ $post->title }}
                            <small style="margin-top: 6px;">{{ $post->reading_time }} min read</small>
                        </span>
                    </a>
                @endforeach
                
                <div style="margin-top: 30px;">
                    <x-ad-slot :ads="$ads" placement="sidebar_ad" label="Sidebar ad" />
                </div>
                
                <div class="newsletter glass" style="margin-top: 30px; text-align: center; padding: 24px;">
                    <h3 style="font-family: Georgia, serif;">Stay Updated</h3>
                    <p class="muted" style="font-size: 14px; margin-bottom: 16px;">Get the latest news and analysis delivered directly to your inbox.</p>
                    <form action="#" method="POST">
                        @csrf
                        <input type="email" placeholder="Your email address" required style="width: 100%; padding: 12px; margin-bottom: 12px; border: 1px solid rgba(199,154,43,0.3); border-radius: 6px;">
                        <button type="submit" class="btn primary" style="width: 100%; padding: 12px; border-radius: 6px; font-weight: bold;">Subscribe</button>
                    </form>
                </div>
            </aside>
        </section>

        <!-- Editor's Picks -->
        <section class="reveal" style="margin-top: 60px;">
            <div class="section-head" style="margin-bottom: 24px;">
                <div>
                    <p class="eyebrow">Featured</p>
                    <h2 style="font-size: 28px; font-family: Georgia, serif;">Editor's Picks</h2>
                </div>
            </div>
            <div class="card-grid responsive-grid-4">
                @foreach($topHeadlines->take(4) as $post)
                    @php
                        $freshImage = $post->featured_image ?: $post->image;
                        if ($freshImage && !str_starts_with($freshImage, 'http') && !str_starts_with($freshImage, '/')) {
                            $freshImage = ltrim($freshImage, '/');
                        }
                    @endphp
                    <article class="card" style="display: flex; flex-direction: column; height: 100%; padding: 0; overflow: hidden; background: rgba(255,255,255,0.9);">
                        @if($freshImage)
                            <a href="{{ $post->publicUrl() }}" style="display: block; height: 200px; overflow: hidden;">
                                <img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->title }}" width="400" height="225" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" loading="lazy" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        @endif
                        <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                            <span style="color: #c79a2b; font-size: 11px; font-weight: 800; text-transform: uppercase;">{{ $post->category?->name }}</span>
                            <h3 style="margin: 10px 0; font-family: Georgia, serif; font-size: 20px; line-height: 1.3;"><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3>
                            <p style="font-size: 14px; color: #5c4f3a; margin-bottom: 16px; flex: 1;">{{ \Illuminate\Support\Str::limit($post->excerpt, 80) }}</p>
                            <small style="color: #8d6a20; font-weight: bold;">{{ optional($post->published_at)->format('M d, Y') }}</small>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Latest by Category -->
        <section class="reveal" style="margin-top: 60px; padding-top: 40px; border-top: 1px solid rgba(199,154,43,0.2);">
            <div class="section-head" style="margin-bottom: 30px;">
                <div>
                    <p class="eyebrow">Discover</p>
                    <h2 style="font-size: 28px; font-family: Georgia, serif;">Latest by Category</h2>
                </div>
            </div>
            
            <div class="responsive-grid-auto">
                @foreach($categories as $category)
                    <div style="background: rgba(255,255,255,0.6); padding: 24px; border-radius: 12px; border: 1px solid rgba(199,154,43,0.15);">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #c79a2b; padding-bottom: 12px; margin-bottom: 20px;">
                            <h3 style="font-family: Georgia, serif; font-size: 22px; margin: 0;">{{ $category->name }}</h3>
                            <a href="{{ route('category.show', $category->slug) }}" style="font-size: 12px; font-weight: bold; color: #c79a2b; text-transform: uppercase;">See All</a>
                        </div>
                        
                        @php
                            $catPosts = $category->blogs;
                        @endphp
                        
                        @if($catPosts->isEmpty())
                            <p class="muted" style="font-size: 14px; font-style: italic;">No posts available yet.</p>
                        @endif
                        
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            @foreach($catPosts as $post)
                                @php
                                    $img = $post->featured_image ?: $post->image;
                                    if ($img && !str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                                        $img = ltrim($img, '/');
                                    }
                                @endphp
                                <div style="display: flex; gap: 16px; align-items: flex-start;">
                                    @if($img)
                                        <a href="{{ $post->publicUrl() }}" style="flex: 0 0 90px;">
                                            <img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->title }}" width="90" height="70" style="width: 90px; height: 70px; object-fit: cover; border-radius: 8px;" loading="lazy" decoding="async">
                                        </a>
                                    @endif
                                    <div>
                                        <h4 style="margin: 0 0 6px 0; font-size: 16px; font-family: Georgia, serif; line-height: 1.3;"><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h4>
                                        <small style="color: #8e7d61; font-size: 12px; font-weight: 600;">{{ optional($post->published_at)->format('M d') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Popular This Week -->
        <section class="reveal" style="margin-top: 60px;">
            <div class="section-head" style="margin-bottom: 24px;">
                <div>
                    <p class="eyebrow">Weekly Top</p>
                    <h2 style="font-size: 28px; font-family: Georgia, serif;">Popular This Week</h2>
                </div>
            </div>
            <div class="fresh-grid responsive-grid-3">
                @foreach($mostRead->take(3) as $post)
                    @php
                        $freshImage = $post->featured_image ?: $post->image;
                        if ($freshImage && !str_starts_with($freshImage, 'http') && !str_starts_with($freshImage, '/')) {
                            $freshImage = ltrim($freshImage, '/');
                        }
                    @endphp
                    <article class="fresh-card" style="display: flex; flex-direction: column;">
                        <a class="story-thumb @unless($freshImage) placeholder @endunless" href="{{ $post->publicUrl() }}" style="height: 220px;">
                            @if($freshImage)
                                <img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->title }}" width="400" height="225" loading="lazy" decoding="async">
                            @else
                                <span>{{ strtoupper(substr($post->category?->name ?? 'MN', 0, 2)) }}</span>
                            @endif
                            <b>{{ $post->category?->name ?? 'News' }}</b>
                        </a>
                        <div class="fresh-copy" style="padding: 24px; flex: 1; display: flex; flex-direction: column;">
                            <span style="color: #c79a2b; margin-bottom: 8px;">{{ optional($post->published_at)->format('M d, Y') }} &bull; {{ $post->reading_time }} min</span>
                            <h3 style="font-size: 22px; margin-bottom: 12px;"><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3>
                            <p style="font-size: 15px; color: #5c4f3a;">{{ \Illuminate\Support\Str::limit($post->excerpt, 100) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

    </div>
</section>
@push('scripts')
<style>
/* Scoped Dark Mode Tweaks for Redesign */
html[data-theme=dark] .sidebar.active-sidebar {
    background: #18140f !important;
    border-color: #4a3718 !important;
}
html[data-theme=dark] .sidebar.active-sidebar .trend {
    border-color: rgba(199,154,43,0.15) !important;
}
html[data-theme=dark] .sidebar.active-sidebar .newsletter.glass {
    background: #13100c !important;
    border-color: #4a3718 !important;
}
html[data-theme=dark] .card {
    background: #18140f !important;
    border-color: #4a3718 !important;
}
html[data-theme=dark] .card p {
    color: #dac99b !important;
}
html[data-theme=dark] section[style*="border-top"] {
    border-color: #4a3718 !important;
}
html[data-theme=dark] section[style*="border-top"] > div > div {
    background: #18140f !important;
    border-color: #4a3718 !important;
}
</style>
<script>
(() => {
    const sliderContainer = document.querySelector('[data-slider]');
    if (!sliderContainer) return;

    const slides = [...sliderContainer.querySelectorAll('.hero-slide')];
    const prevBtn = sliderContainer.querySelector('[data-slide-prev]');
    const nextBtn = sliderContainer.querySelector('[data-slide-next]');
    
    if (slides.length <= 1) {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        return;
    } else {
        if (prevBtn) prevBtn.style.display = 'flex';
        if (nextBtn) nextBtn.style.display = 'flex';
    }

    let index = 0;
    let slideInterval = null;

    const show = next => {
        slides[index]?.classList.remove('active');
        index = (next + slides.length) % slides.length;
        slides[index]?.classList.add('active');
    };

    // Click handler on the slide itself (excluding link clicks)
    slides.forEach(slide => {
        slide.addEventListener('click', (e) => {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                const url = slide.getAttribute('data-article-url');
                if (url) {
                    window.location.href = url;
                }
            }
        });
    });

    nextBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        show(index + 1);
    });

    prevBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        show(index - 1);
    });

    // Auto Play with pause-on-hover
    const startAutoPlay = () => {
        if (!slideInterval && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            slideInterval = setInterval(() => show(index + 1), 6000);
        }
    };

    const stopAutoPlay = () => {
        if (slideInterval) {
            clearInterval(slideInterval);
            slideInterval = null;
        }
    };

    sliderContainer.addEventListener('mouseenter', stopAutoPlay);
    sliderContainer.addEventListener('mouseleave', startAutoPlay);

    // Touch Swipe support for mobile devices
    let touchStartX = 0;
    let touchEndX = 0;

    sliderContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].clientX;
    }, { passive: true });

    sliderContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        const threshold = 55;
        if (touchEndX < touchStartX - threshold) {
            show(index + 1); // Swipe left -> Next
        } else if (touchEndX > touchStartX + threshold) {
            show(index - 1); // Swipe right -> Prev
        }
    }, { passive: true });

    // Keyboard support
    document.addEventListener('keydown', (e) => {
        const rect = sliderContainer.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom >= 0;
        if (!isVisible) return;

        if (e.key === 'ArrowRight') {
            show(index + 1);
        } else if (e.key === 'ArrowLeft') {
            show(index - 1);
        }
    });

    startAutoPlay();

    // Intersection observer for fade/reveal components
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => entry.target.classList.toggle('visible', entry.isIntersecting));
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    } else {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
    }
})();
</script>
@endpush
@endsection
