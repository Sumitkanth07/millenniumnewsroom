@extends('emails.newsletter.layouts.newsletter', [
    'subject' => $blog->title,
    'headerSubtitle' => 'Fresh Published Article',
    'subscriber' => $subscriber ?? null,
])

@section('content')
    <div style="margin-bottom: 8px;">
        <span class="category-pill">{{ $blog->category?->name ?: 'NEWS' }}</span>
        @if($blog->published_at)
            <span style="font-size: 12px; color: #888888; float: right; margin-top: 2px;">
                {{ $blog->published_at->format('M d, Y') }}
            </span>
        @endif
    </div>

    <h1 class="article-title">
        <a href="{{ $articleUrl }}">{{ $blog->title }}</a>
    </h1>

    @if(!empty($imageUrl))
        <div class="featured-image-container">
            <a href="{{ $articleUrl }}">
                <img src="{{ $imageUrl }}" alt="{{ $blog->title }}" style="width: 100%; max-height: 340px; object-fit: cover; border-radius: 6px;">
            </a>
        </div>
    @endif

    <div class="article-excerpt">
        {{ $blog->excerpt ?: Str::limit(strip_tags($blog->content), 200) }}
    </div>

    <div style="text-align: center; margin-top: 28px; margin-bottom: 10px;">
        <a href="{{ $articleUrl }}" class="btn-primary">READ FULL STORY &rarr;</a>
    </div>
@endsection
