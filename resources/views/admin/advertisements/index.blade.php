@extends('admin.layout')
@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Advertisements</span>
        <h2>Placements Manager</h2>
    </div>
    <div class="actions" style="display: flex; gap: 10px;">
        <a class="btn" href="{{ route('admin.advertisements.dashboard') }}">Dashboard</a>
        <a class="btn primary" href="{{ route('admin.advertisements.create') }}">New Advertisement</a>
    </div>
</div>

<div class="panel">
    @if($ads->isEmpty())
        <p class="muted">No advertisements configured. Create one to begin monetizing your site.</p>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1); font-weight: bold; opacity: 0.8;">
                        <th style="padding: 12px 8px;">Ad Name</th>
                        <th style="padding: 12px 8px;">Placement</th>
                        <th style="padding: 12px 8px;">Target Device</th>
                        <th style="padding: 12px 8px;">Type</th>
                        <th style="padding: 12px 8px; text-align: center;">Priority</th>
                        <th style="padding: 12px 8px; text-align: center;">Views</th>
                        <th style="padding: 12px 8px; text-align: center;">Clicks</th>
                        <th style="padding: 12px 8px; text-align: center;">CTR</th>
                        <th style="padding: 12px 8px; text-align: center;">Status</th>
                        <th style="padding: 12px 8px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ads as $ad)
                        @php
                            $ctr = $ad->current_views > 0 ? round(($ad->current_clicks / $ad->current_views) * 100, 2) : 0;
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;">
                            <td style="padding: 12px 8px; font-weight: 600;">{{ $ad->name }}</td>
                            <td style="padding: 12px 8px;"><code style="background: rgba(199,154,43,0.1); color: #c79a2b; padding: 2px 6px; border-radius: 4px; font-size: 13px;">{{ $ad->placement }}</code></td>
                            <td style="padding: 12px 8px; text-transform: capitalize;">{{ $ad->device }}</td>
                            <td style="padding: 12px 8px;">
                                <span style="font-size: 12px; font-weight: bold; text-transform: uppercase; opacity: 0.8;">{{ str_replace('_', ' ', $ad->type) }}</span>
                            </td>
                            <td style="padding: 12px 8px; text-align: center;">{{ $ad->priority }}</td>
                            <td style="padding: 12px 8px; text-align: center;">{{ number_format($ad->current_views) }}</td>
                            <td style="padding: 12px 8px; text-align: center;">{{ number_format($ad->current_clicks) }}</td>
                            <td style="padding: 12px 8px; text-align: center; color: #0275d8; font-weight: 600;">{{ $ctr }}%</td>
                            <td style="padding: 12px 8px; text-align: center;">
                                @if($ad->is_active && $ad->isScheduled() && $ad->isUnderLimits())
                                    <span class="btn small" style="background: rgba(92,184,92,0.1); color: #5cb85c; border: 0; padding: 2px 6px; border-radius: 4px; pointer-events: none; font-size: 11px;">Serving</span>
                                @else
                                    <span class="btn small" style="background: rgba(217,83,79,0.1); color: #d9534f; border: 0; padding: 2px 6px; border-radius: 4px; pointer-events: none; font-size: 11px;">Inactive</span>
                                @endif
                            </td>
                            <td style="padding: 12px 8px; text-align: right;" class="actions">
                                <a class="btn small" href="{{ route('admin.advertisements.edit', $ad) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.advertisements.destroy', $ad) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn small danger" style="padding: 4px 8px; font-size: 12px;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $ads->links() }}
        </div>
    @endif
</div>
@endsection
