@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center;">
    <div style="font-size: 48px; color: #1e8e3e; margin-bottom: 12px;">✓</div>
    <h1 style="font-size: 24px; font-weight: 800; color: #1f1a12; margin-bottom: 10px;">You Have Been Unsubscribed</h1>
    <p style="font-size: 15px; color: #555; line-height: 1.6; margin-bottom: 24px;">
        <strong>{{ $subscriber->email }}</strong> has been removed from all MILLENNIUM NEWSROOM email broadcasts.
    </p>

    <div style="font-size: 14px; color: #777;">
        Unsubscribed by mistake? You can <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}" style="color: #c79a2b; text-decoration: underline;">re-subscribe or manage preferences anytime</a>.
    </div>

    <div style="margin-top: 30px;">
        <a href="{{ config('app.url') }}" style="display: inline-block; background: #1f1a12; color: #fff; padding: 10px 22px; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
            Return to Website
        </a>
    </div>
</div>
@endsection
