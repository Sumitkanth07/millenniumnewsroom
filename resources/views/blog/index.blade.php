@extends('frontend.layout')

@section('content')

<section class="page-hero">
    <p class="eyebrow">MILLENNIUM NEWSROOM</p>

    <h1>Latest News</h1>

    <p>
        Business, markets, politics, technology, lifestyle and opinion coverage in one newsroom.
    </p>
</section>

<section class="news-shell">

    <div class="card-grid">

        @foreach($blogs as $blog)

            <article class="card">

                @if($blog->featured_image || $blog->image)

                    <img 
                        class="thumb"
                        src="{{ asset($blog->featured_image ?: $blog->image) }}"
                        alt="{{ $blog->title }}"
                        loading="lazy">

                @endif

                <span>{{ $blog->category?->name }}</span>

                <h2>
                    <a href="{{ $blog->publicUrl() }}">
                        {{ $blog->title }}
                    </a>
                </h2>

                <p>{{ $blog->excerpt }}</p>

            </article>

        @endforeach

    </div>

    {{ $blogs->links() }}

</section>

@endsection
