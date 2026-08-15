@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 800; color: #1f1a12; margin-bottom: 6px;">Email Preferences</h1>
        <p style="font-size: 14px; color: #666;">Subscriber: <strong>{{ $subscriber->email }}</strong></p>
    </div>

    @if(session('status'))
        <div style="background-color: #e6f4ea; color: #137333; padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; font-weight: 600;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('newsletter.preferences.update', $subscriber->unsubscribe_token) }}">
        @csrf
        
        <div style="margin-bottom: 20px; padding: 16px; background-color: #fafafa; border-radius: 6px; border: 1px solid #eee;">
            <label style="display: flex; align-items: flex-start; cursor: pointer;">
                <input type="checkbox" name="notify_new_post" value="1" {{ $subscriber->notify_new_post ? 'checked' : '' }} style="margin-top: 3px; margin-right: 12px; width: 18px; height: 18px;">
                <div>
                    <strong style="font-size: 15px; color: #1f1a12; display: block;">New Article Notifications</strong>
                    <span style="font-size: 13px; color: #666; line-height: 1.4; display: block; margin-top: 2px;">
                        Receive instant email notifications whenever a breaking or featured article is published.
                    </span>
                </div>
            </label>
        </div>

        <div style="margin-bottom: 24px; padding: 16px; background-color: #fafafa; border-radius: 6px; border: 1px solid #eee;">
            <label style="display: flex; align-items: flex-start; cursor: pointer;">
                <input type="checkbox" name="notify_weekly_digest" value="1" {{ $subscriber->notify_weekly_digest ? 'checked' : '' }} style="margin-top: 3px; margin-right: 12px; width: 18px; height: 18px;">
                <div>
                    <strong style="font-size: 15px; color: #1f1a12; display: block;">Weekly Monday Digest</strong>
                    <span style="font-size: 13px; color: #666; line-height: 1.4; display: block; margin-top: 2px;">
                        Receive our executive weekly briefing every Monday at 5:00 AM IST grouping the top business, market and policy stories of the week.
                    </span>
                </div>
            </label>
        </div>

        <div style="margin-bottom: 24px; text-align: center;">
            <button type="submit" style="background-color: #1f1a12; color: #fff; border: none; padding: 12px 28px; font-size: 14px; font-weight: 700; border-radius: 6px; cursor: pointer; letter-spacing: 0.5px;">
                Save Preferences
            </button>
        </div>

        <div style="border-top: 1px solid #eee; padding-top: 20px; text-align: center;">
            <button type="submit" name="unsubscribe_all" value="1" style="background: none; border: none; color: #d93025; font-size: 13px; text-decoration: underline; cursor: pointer;">
                Unsubscribe completely from all newsletters
            </button>
        </div>
    </form>
</div>
@endsection
