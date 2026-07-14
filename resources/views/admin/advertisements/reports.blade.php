@extends('admin.layout')
@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Advertisements</span>
        <h2>Performance Reports</h2>
    </div>
    <div class="actions" style="display: flex; gap: 10px;">
        <a class="btn" href="{{ route('admin.advertisements.dashboard') }}">Dashboard</a>
        <a class="btn" href="{{ route('admin.advertisements.index') }}">Placements</a>
    </div>
</div>

<div class="panel">
    @if($ads->isEmpty())
        <p class="muted">No advertisements found to report on.</p>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1); font-weight: bold; opacity: 0.8;">
                        <th style="padding: 12px 8px;">Ad Name</th>
                        <th style="padding: 12px 8px; text-align: center;">Views</th>
                        <th style="padding: 12px 8px; text-align: center;">Clicks</th>
                        <th style="padding: 12px 8px; text-align: center;">CTR</th>
                        <th style="padding: 12px 8px; text-align: center;">Max Views</th>
                        <th style="padding: 12px 8px; text-align: center;">Max Clicks</th>
                        <th style="padding: 12px 8px;">Last View</th>
                        <th style="padding: 12px 8px;">Last Click</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ads as $ad)
                        @php
                            $ctr = $ad->current_views > 0 ? round(($ad->current_clicks / $ad->current_views) * 100, 2) : 0;
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 8px;">
                                <strong>{{ $ad->name }}</strong>
                                <span style="display: block; font-size: 11px; opacity: 0.6;">Placement: {{ $ad->placement }}</span>
                            </td>
                            <td style="padding: 12px 8px; text-align: center;">{{ number_format($ad->current_views) }}</td>
                            <td style="padding: 12px 8px; text-align: center;">{{ number_format($ad->current_clicks) }}</td>
                            <td style="padding: 12px 8px; text-align: center; color: #0275d8; font-weight: 600;">{{ $ctr }}%</td>
                            <td style="padding: 12px 8px; text-align: center; opacity: 0.8;">{{ $ad->max_views ?? 'Unlimited' }}</td>
                            <td style="padding: 12px 8px; text-align: center; opacity: 0.8;">{{ $ad->max_clicks ?? 'Unlimited' }}</td>
                            <td style="padding: 12px 8px; font-size: 13px;">{{ $ad->last_viewed_at ? $ad->last_viewed_at->diffForHumans() : 'Never' }}</td>
                            <td style="padding: 12px 8px; font-size: 13px;">{{ $ad->last_clicked_at ? $ad->last_clicked_at->diffForHumans() : 'Never' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
