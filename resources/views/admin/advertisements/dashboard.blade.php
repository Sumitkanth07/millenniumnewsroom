@extends('admin.layout')
@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Advertisements</span>
        <h2>Dashboard</h2>
    </div>
    <div class="actions" style="display: flex; gap: 10px;">
        <a class="btn" href="{{ route('admin.advertisements.settings') }}">Ad Settings</a>
        <a class="btn" href="{{ route('admin.advertisements.reports') }}">Ad Reports</a>
        <a class="btn primary" href="{{ route('admin.advertisements.create') }}">Create Advertisement</a>
    </div>
</div>

<div class="panel" style="margin-bottom: 30px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px;">
        <div style="padding: 20px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 14px; opacity: 0.7; margin-bottom: 5px;">Total Ads</div>
            <div style="font-size: 32px; font-weight: bold; color: #c79a2b;">{{ $totalAds }}</div>
        </div>
        <div style="padding: 20px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 14px; opacity: 0.7; margin-bottom: 5px;">Active Ads</div>
            <div style="font-size: 32px; font-weight: bold; color: #5cb85c;">{{ $activeAds }}</div>
        </div>
        <div style="padding: 20px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 14px; opacity: 0.7; margin-bottom: 5px;">Total Views</div>
            <div style="font-size: 32px; font-weight: bold;">{{ number_format($totalViews) }}</div>
        </div>
        <div style="padding: 20px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 14px; opacity: 0.7; margin-bottom: 5px;">Total Clicks</div>
            <div style="font-size: 32px; font-weight: bold;">{{ number_format($totalClicks) }}</div>
        </div>
        <div style="padding: 20px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); text-align: center;">
            <div style="font-size: 14px; opacity: 0.7; margin-bottom: 5px;">Average CTR</div>
            <div style="font-size: 32px; font-weight: bold; color: #0275d8;">{{ $avgCtr }}%</div>
        </div>
    </div>
</div>

<div class="admin-title">
    <div>
        <h2>Active Placements Overview</h2>
    </div>
</div>

<div class="panel">
    @if($placements->isEmpty())
        <p class="muted">No advertisements have been created yet.</p>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            @foreach($placements as $placement)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border-radius: 8px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                    <div style="font-weight: 600;">{{ ucwords(str_replace('_', ' ', $placement->placement)) }}</div>
                    <span class="btn small" style="background: rgba(199,154,43,0.1); color: #c79a2b; border: 0; padding: 4px 10px; border-radius: 4px; font-size: 12px;">{{ $placement->count }} Ads</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
