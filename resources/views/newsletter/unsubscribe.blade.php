@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 800; color: #1f1a12; margin-bottom: 6px;">MILLENNIUM NEWSROOM</h1>
        <p style="font-size: 14px; color: #666;">Newsletter Subscription Management</p>
    </div>

    @if(!empty($error))
        <div style="background-color: #fce8e6; color: #a50e0e; padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 20px;">
            {{ $error }}
        </div>
    @elseif($subscriber)
        <div style="text-align: center; margin-bottom: 24px;">
            <p style="font-size: 16px; color: #333; line-height: 1.5;">
                Are you sure you want to unsubscribe <strong>{{ $subscriber->email }}</strong> from MILLENNIUM NEWSROOM email updates?
            </p>
        </div>

        <form method="POST" action="{{ route('newsletter.unsubscribe.post', $subscriber->unsubscribe_token) }}" style="text-align: center;">
            @csrf
            <button type="submit" style="background-color: #d93025; color: #fff; border: none; padding: 12px 28px; font-size: 14px; font-weight: 700; border-radius: 6px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;">
                Confirm Unsubscribe
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 13px;">
            <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}" style="color: #c79a2b; text-decoration: underline;">
                Or customize your email preferences
            </a>
        </div>
    @endif
</div>
@endsection
