@extends('emails.newsletter.layouts.newsletter', [
    'subject' => 'Weekly Digest | MILLENNIUM NEWSROOM',
    'headerSubtitle' => 'Weekly News Briefing & Executive Summary',
    'subscriber' => $subscriber ?? null,
])

@section('content')
    <div style="text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #1f1a12;">
        <h2 style="font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin: 0; color: #1f1a12;">
            WEEKLY DIGEST
        </h2>
        <div style="font-size: 13px; color: #666666; margin-top: 4px;">
            Top stories and executive coverage from the past week
        </div>
    </div>

    @if(empty($groupedBlogs) || $groupedBlogs->isEmpty())
        <p style="text-align: center; color: #666666;">No new articles were published during the past week.</p>
    @else
        @foreach($groupedBlogs as $categoryName => $blogs)
            <div style="margin-bottom: 32px;">
                <div style="background-color: #1f1a12; color: #ffffff; padding: 6px 14px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; border-radius: 4px; display: inline-block; margin-bottom: 16px;">
                    {{ $categoryName }}
                </div>

                @foreach($blogs as $blog)
                    @php
                        $articleUrl = \App\Helpers\NewsletterHelper::getAbsoluteArticleUrl($blog);
                        $imgUrl = \App\Helpers\NewsletterHelper::getAbsoluteImageUrl($blog->featured_image ?: $blog->image);
                    @endphp
                    <div style="margin-bottom: 22px; padding-bottom: 18px; border-bottom: 1px dashed #e0e0e0;">
                        @if(!empty($imgUrl))
                            <div style="margin-bottom: 10px;">
                                <a href="{{ $articleUrl }}">
                                    <img src="{{ $imgUrl }}" alt="{{ $blog->title }}" style="width: 100%; max-height: 220px; object-fit: cover; border-radius: 5px;">
                                </a>
                            </div>
                        @endif

                        <h3 style="font-size: 17px; font-weight: 700; line-height: 1.4; margin: 0 0 8px 0;">
                            <a href="{{ $articleUrl }}" style="color: #1f1a12; text-decoration: none;">
                                {{ $blog->title }}
                            </a>
                        </h3>

                        <div style="font-size: 13px; color: #555555; line-height: 1.5; margin-bottom: 10px;">
                            {{ $blog->excerpt ?: Str::limit(strip_tags($blog->content), 140) }}
                        </div>

                        <div>
                            <a href="{{ $articleUrl }}" style="font-size: 12px; font-weight: 700; color: #c79a2b; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                Read Article &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
@endsection
