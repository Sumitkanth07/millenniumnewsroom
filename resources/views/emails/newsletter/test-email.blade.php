@extends('emails.newsletter.layouts.newsletter', [
    'subject' => 'Test Email | MILLENNIUM NEWSROOM Newsletter',
    'headerSubtitle' => 'Production System Test',
    'subscriber' => null,
])

@section('content')
    <div style="background-color: #f7f3e8; border-left: 4px solid #c79a2b; padding: 14px; margin-bottom: 20px; border-radius: 4px;">
        <strong style="color: #1f1a12; font-size: 14px;">Newsletter System Test Email</strong>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #555555;">
            This is a test broadcast dispatched from the Millennium Newsroom Newsletter Management module.
        </p>
    </div>

    <span class="category-pill">SAMPLE CATEGORY</span>
    <h1 class="article-title">Sample Article Title for Newsletter Formatting Verification</h1>

    <div class="article-excerpt">
        This is a sample article summary demonstrating the production newsletter layout typography, brand styling, responsive image formatting, and footer compliance.
    </div>

    <div style="text-align: center; margin-top: 24px;">
        <a href="{{ config('app.url') }}" class="btn-primary">VISIT MILLENNIUM NEWSROOM &rarr;</a>
    </div>
@endsection
