{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
@php($appUrl = rtrim(config('app.url'), '/'))
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ $appUrl }}/</loc><changefreq>hourly</changefreq><priority>1.0</priority></url>
    <url><loc>{{ $appUrl }}/blog</loc><changefreq>hourly</changefreq><priority>0.8</priority></url>
    @foreach($blogs as $blog)
        <url><loc>{{ $appUrl }}/{{ $blog->category?->slug ?: 'news' }}/{{ $blog->slug }}</loc><lastmod>{{ $blog->updated_at->toAtomString() }}</lastmod><changefreq>daily</changefreq><priority>0.9</priority></url>
    @endforeach
    @foreach($categories ?? [] as $category)
        <url><loc>{{ $appUrl }}/category/{{ $category->slug }}</loc><lastmod>{{ $category->updated_at->toAtomString() }}</lastmod><changefreq>daily</changefreq><priority>0.7</priority></url>
    @endforeach
    @foreach($pages ?? [] as $page)
        <url><loc>{{ $appUrl }}/page/{{ $page->slug }}</loc><lastmod>{{ $page->updated_at->toAtomString() }}</lastmod><changefreq>monthly</changefreq><priority>0.5</priority></url>
    @endforeach
</urlset>
