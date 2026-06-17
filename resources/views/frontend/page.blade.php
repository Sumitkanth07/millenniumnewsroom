@extends('frontend.layout')
@section('content')
<article class="article">
    @if($page->banner_image)<img class="article-image" src="{{ asset($page->banner_image) }}" alt="{{ $page->title }}" loading="eager" decoding="async">@endif
    <h1>{{ $page->title }}</h1>
    <div class="content">{!! $page->content !!}</div>
</article>
@endsection
