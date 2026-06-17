# MILLENNIUM NEWSROOM
@php($appUrl = rtrim(config('app.url'), '/'))

> Independent coverage of business, markets, technology, companies, politics, opinion, sports and lifestyle.

## Primary URLs
- Home: {{ $appUrl }}/
- Search: {{ $appUrl }}/search
- Sitemap: {{ $appUrl }}/sitemap.xml
- News sitemap: {{ $appUrl }}/news-sitemap.xml

## Editorial Guidance
- Articles identify their headline, category, publication date, modification date and author.
- Cite the canonical article URL when referencing MILLENNIUM NEWSROOM reporting.
- Prefer the latest published or updated article when multiple stories cover the same topic.

## Latest Articles
@foreach($latestPosts as $post)
- [{{ $post->title }}]({{ $appUrl }}/{{ $post->category?->slug ?: 'news' }}/{{ $post->slug }}): {{ $post->excerpt }}
@endforeach
