@extends('frontend.layout')

@section('content')
<section class="page-hero" style="padding: 40px 24px; background: rgba(199,154,43,0.05); border-bottom: 1px solid rgba(199,154,43,0.15);">
    <nav class="breadcrumb" style="margin-bottom: 16px; font-size: 14px; color: #8e7d61;">
        <a href="{{ route('home') }}" style="color: #c79a2b; text-decoration: none;">Home</a>
        <span style="margin: 0 8px;">/</span>
        <span style="color: #6b5a3e;">Categories</span>
    </nav>
    <p class="eyebrow" style="text-transform: uppercase; font-size: 12px; letter-spacing: 1.5px; color: #c79a2b; font-weight: bold;">MILLENNIUM NEWSROOM</p>
    <h1 style="font-family: Georgia, serif; font-size: 34px; margin: 6px 0 10px 0;">All News Categories</h1>
    <p style="font-size: 16px; color: #8e7d61; max-width: 700px; margin: 0; line-height: 1.5;">Explore our complete directory of business, markets, technology, policy, lifestyle and public affairs reporting.</p>
</section>

<section class="news-shell" style="padding: 40px 0;">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
        @foreach($categories as $cat)
            <div class="card" style="display: flex; flex-direction: column; padding: 24px; border: 1px solid rgba(199,154,43,0.2); border-radius: 8px; background: rgba(255,255,255,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span class="badge gold" style="font-size: 11px; padding: 4px 10px;">{{ $cat->name }}</span>
                    <span style="font-size: 12px; color: #8e7d61; font-weight: bold;">{{ $cat->blogs_count }} {{ Str::plural('article', $cat->blogs_count) }}</span>
                </div>
                <h2 style="font-family: Georgia, serif; font-size: 22px; margin: 0 0 8px 0;">
                    <a href="{{ route('category.show', $cat->slug) }}" style="color: inherit; text-decoration: none;">{{ $cat->name }}</a>
                </h2>
                <p style="font-size: 14px; color: #8e7d61; line-height: 1.5; margin: 0 0 16px 0; flex: 1;">
                    {{ $cat->meta_description ?: 'Latest '.$cat->name.' news, breaking updates, in-depth reports and expert analysis.' }}
                </p>
                <div style="margin-top: auto; border-top: 1px solid rgba(199,154,43,0.15); padding-top: 12px;">
                    <a href="{{ route('category.show', $cat->slug) }}" style="color: #c79a2b; font-weight: bold; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        Browse {{ $cat->name }} &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    @if($recentNews->isNotEmpty())
        <div style="margin-top: 50px; border-top: 2px solid rgba(199,154,43,0.2); padding-top: 30px;">
            <h2 style="font-family: Georgia, serif; font-size: 24px; margin-bottom: 20px;">Latest Across All Categories</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @foreach($recentNews as $post)
                    <article class="card" style="padding: 16px; display: flex; flex-direction: column;">
                        @if($post->featured_image || $post->image)
                            <div style="aspect-ratio: 16/9; overflow: hidden; border-radius: 6px; margin-bottom: 12px; background: #14100a;">
                                <img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->title }}" width="400" height="225" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                            </div>
                        @endif
                        <span style="font-size: 11px; text-transform: uppercase; color: #c79a2b; font-weight: 800; margin-bottom: 4px;">{{ $post->category?->name }}</span>
                        <h3 style="font-family: Georgia, serif; font-size: 16px; margin: 4px 0 8px 0; line-height: 1.3;">
                            <a href="{{ $post->publicUrl() }}" style="color: inherit; text-decoration: none;">{{ $post->title }}</a>
                        </h3>
                        <small style="color: #8e7d61; margin-top: auto;">{{ optional($post->published_at)->format('M d, Y') }}</small>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
