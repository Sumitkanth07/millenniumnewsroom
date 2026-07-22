@extends('frontend.layout')
@section('content')
<section class="page-hero"><p class="eyebrow">Search</p><h1>News Search</h1></section>
<section class="news-shell search-container">
    <form class="search-form-card" method="GET" action="{{ route('search') }}">
        <div class="search-input-group">
            <div class="search-input-wrapper">
                <span class="search-input-icon">🔍</span>
                <input name="q" value="{{ $query }}" placeholder="Search articles, companies, topics...">
            </div>
            <div class="search-select-wrapper">
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-select-wrapper">
                <select name="sort">
                    <option value="latest" @selected($sort === 'latest')>Latest</option>
                    <option value="popular" @selected($sort === 'popular')>Popular</option>
                </select>
            </div>
            <div class="search-btn-wrapper">
                <button class="search-submit-btn" type="submit">Search</button>
            </div>
        </div>
    </form>
    <div class="article-list">
        @forelse($posts as $post)
            @php($image = $post->featured_image ?: $post->image)
            <article class="list-card">
                @if($image)<img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->featured_image_alt ?: $post->title }}" width="800" height="450" loading="lazy" decoding="async">@endif
                <div><span>{{ $post->category?->name }}</span>   <h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3><p>{{ $post->excerpt }}</p></div>
            </article>
        @empty
            <div class="card empty-state" style="margin-bottom: 30px; text-align: center; padding: 30px 20px;">
                <h2 style="font-family: Georgia, serif; font-size: 24px; color: #c79a2b; margin-bottom: 8px;">No results found</h2>
                <p style="color: #8e7d61; font-size: 15px;">Try another keyword or category, or browse recommended stories below.</p>
            </div>
            @if(isset($fallbackPosts) && $fallbackPosts->isNotEmpty())
                <div style="margin-top: 30px;">
                    <h2 style="font-family: Georgia, serif; font-size: 22px; border-bottom: 2px solid #c79a2b; padding-bottom: 8px; margin-bottom: 20px;">Recommended News</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                        @foreach($fallbackPosts as $fallbackPost)
                            <article class="card" style="display: flex; flex-direction: column; padding: 15px;">
                                @if($fallbackPost->featured_image || $fallbackPost->image)
                                    <div style="aspect-ratio: 16/9; overflow: hidden; border-radius: 6px; margin-bottom: 10px; background: #120f0b;">
                                        <img src="{{ $fallbackPost->getThumbnailUrl() }}" alt="{{ $fallbackPost->title }}" width="400" height="225" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                    </div>
                                @endif
                                <span style="font-size: 11px; text-transform: uppercase; color: #c79a2b; font-weight: 800;">{{ $fallbackPost->category?->name }}</span>
                                <h3 style="font-family: Georgia, serif; font-size: 16px; margin: 4px 0 6px 0; line-height: 1.3;"><a href="{{ $fallbackPost->publicUrl() }}" style="color: inherit; text-decoration: none;">{{ $fallbackPost->title }}</a></h3>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforelse
    </div>
    <div style="margin-top: 24px;">
        {{ $posts->links() }}
    </div>
</section>
@endsection
