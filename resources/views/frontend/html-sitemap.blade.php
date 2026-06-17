@extends('frontend.layout')
@section('content')
<section class="page-hero sitemap-hero"><p class="eyebrow">Explore</p><h1>MILLENNIUM NEWSROOM Sitemap</h1><p>Browse the full website structure without opening the XML sitemap used by search engines.</p></section>
<section class="news-shell">
    <div class="sitemap-filter"><input type="search" placeholder="Filter sitemap links" data-sitemap-filter></div>
    <div class="sitemap-grid" data-sitemap-list>
        <section class="sitemap-panel category-card-panel"><h2>Categories</h2>@foreach($categories as $category)<a href="{{ route('category.show', $category) }}">{{ $category->name }} <span>{{ $category->blogs_count }}</span></a>@endforeach</section>
        <section class="sitemap-panel"><h2>Popular Pages</h2>@foreach($pages as $page)<a href="{{ route('page.show', $page) }}">{{ $page->title }}</a>@endforeach</section>
        <section class="sitemap-panel wide"><h2>Latest Posts</h2>@foreach($latestPosts as $post)<a href="{{ $post->publicUrl() }}">{{ $post->title }} <span>{{ optional($post->published_at)->format('M d, Y') }}</span></a>@endforeach</section>
        <section class="sitemap-panel"><h2>Popular Posts</h2>@foreach($popularPosts as $post)<a href="{{ $post->publicUrl() }}">{{ $post->title }} <span>{{ optional($post->published_at)->format('M d, Y') }}</span></a>@endforeach</section>
        <section class="sitemap-panel"><h2>Archives</h2>@foreach($archives as $archive)<a href="{{ route('category.show', \Illuminate\Support\Str::slug($archive)) }}">{{ $archive }}</a>@endforeach</section>
    </div>
</section>
@push('scripts')
<script>
document.querySelector('[data-sitemap-filter]')?.addEventListener('input', event => {
    const needle = event.target.value.toLowerCase();
    document.querySelectorAll('[data-sitemap-list] a').forEach(link => {
        link.style.display = link.textContent.toLowerCase().includes(needle) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
