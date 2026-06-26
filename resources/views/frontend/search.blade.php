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
                @if($image)<img src="{{ $post->getThumbnailUrl() }}" alt="{{ $post->featured_image_alt ?: $post->title }}" loading="lazy" decoding="async">@endif
                <div><span>{{ $post->category?->name }}</span>   <h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3><p>{{ $post->excerpt }}</p></div>
            </article>
        @empty
            <div class="card empty-state"><h2>No results found</h2><p>Try another keyword or category.</p></div>
        @endforelse
    </div>
    <div style="margin-top: 24px;">
        {{ $posts->links() }}
    </div>
</section>
@endsection
