@extends('admin.layout')

@section('content')
<div style="padding: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: 800; margin: 0; color: #fff;">Newsletter Email Logs</h1>
        <a href="{{ route('admin.newsletter.dashboard') }}" style="background: rgba(255,255,255,0.1); color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;">
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- Log Stats Header -->
    <div style="display: flex; gap: 16px; margin-bottom: 20px;">
        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 12px 20px; min-width: 140px;">
            <div style="font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700;">Total Logs</div>
            <div style="font-size: 24px; font-weight: 800; color: #fff;">{{ number_format($stats['total']) }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 12px 20px; min-width: 140px;">
            <div style="font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700;">Sent</div>
            <div style="font-size: 24px; font-weight: 800; color: #2ecc71;">{{ number_format($stats['sent']) }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 12px 20px; min-width: 140px;">
            <div style="font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700;">Failed</div>
            <div style="font-size: 24px; font-weight: 800; color: #e74c3c;">{{ number_format($stats['failed']) }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 12px 20px; min-width: 140px;">
            <div style="font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700;">Queued</div>
            <div style="font-size: 24px; font-weight: 800; color: #f39c12;">{{ number_format($stats['queued']) }}</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <form method="GET" action="{{ route('admin.newsletter.logs') }}" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by subscriber email..." style="max-width: 300px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
        
        <select name="status" style="background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            <option value="">All Statuses</option>
            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>Queued</option>
        </select>

        <select name="type" style="background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            <option value="">All Types</option>
            <option value="new_post" {{ request('type') === 'new_post' ? 'selected' : '' }}>New Post</option>
            <option value="weekly_digest" {{ request('type') === 'weekly_digest' ? 'selected' : '' }}>Weekly Digest</option>
            <option value="test" {{ request('type') === 'test' ? 'selected' : '' }}>Test Email</option>
        </select>

        <button type="submit" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">Filter</button>
        @if(request()->hasAny(['search', 'status', 'type']))
            <a href="{{ route('admin.newsletter.logs') }}" style="background: rgba(255,255,255,0.05); color: #ccc; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; display: flex; align-items: center;">Clear</a>
        @endif
    </form>

    <!-- Logs Table -->
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; color: #fff;">
            <thead>
                <tr style="background: rgba(0,0,0,0.3); border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">Subscriber</th>
                    <th style="padding: 12px;">Campaign Type</th>
                    <th style="padding: 12px;">Article / Details</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Sent At</th>
                    <th style="padding: 12px;">Error Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $l)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 12px;">#{{ $l->id }}</td>
                        <td style="padding: 12px; font-weight: 600;">{{ $l->subscriber?->email ?: 'Recipient N/A' }}</td>
                        <td style="padding: 12px;">
                            <span style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px; font-size: 11px;">
                                {{ strtoupper(str_replace('_', ' ', $l->campaign_type)) }}
                            </span>
                        </td>
                        <td style="padding: 12px; color: rgba(255,255,255,0.8);">
                            {{ $l->blog?->title ?: ($l->campaign?->title ?: 'System Notification') }}
                        </td>
                        <td style="padding: 12px;">
                            @php
                                $statusColors = ['sent' => '#2ecc71', 'failed' => '#e74c3c', 'queued' => '#f39c12'];
                            @endphp
                            <span style="color: {{ $statusColors[$l->status] ?? '#fff' }}; font-weight: 700; font-size: 11px; text-transform: uppercase;">
                                {{ $l->status }}
                            </span>
                        </td>
                        <td style="padding: 12px; color: rgba(255,255,255,0.6);">
                            {{ $l->sent_at ? $l->sent_at->format('M d, Y H:i:s') : '-' }}
                        </td>
                        <td style="padding: 12px; color: #ff6b6b; font-size: 11px; max-width: 250px; word-break: break-word;">
                            {{ $l->error_message ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center; color: rgba(255,255,255,0.5);">No email logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection
