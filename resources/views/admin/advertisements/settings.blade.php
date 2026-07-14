@extends('admin.layout')
@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Advertisements</span>
        <h2>Global Ad & Tracker Settings</h2>
    </div>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.advertisements.save-settings') }}">
        @csrf
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="adsense_client_id" style="display: block; font-weight: bold; margin-bottom: 5px;">Google AdSense Publisher ID</label>
            <input type="text" id="adsense_client_id" name="adsense_client_id" class="form-control" value="{{ $settings['adsense_client_id'] }}" placeholder="e.g. ca-pub-4398486915982313" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            <small style="opacity: 0.6; display: block; margin-top: 5px;">When entered, the AdSense script will be dynamically generated and loaded inside the HTML head on all pages automatically.</small>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="google_analytics_code" style="display: block; font-weight: bold; margin-bottom: 5px;">Google Analytics (gtag.js) Script Code</label>
            <textarea id="google_analytics_code" name="google_analytics_code" rows="5" class="form-control" placeholder="&lt;script async src='...'&gt;&lt;/script&gt;&#10;&lt;script&gt;...&lt;/script&gt;" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ $settings['google_analytics_code'] }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="google_tag_manager_code" style="display: block; font-weight: bold; margin-bottom: 5px;">Google Tag Manager Script Code</label>
            <textarea id="google_tag_manager_code" name="google_tag_manager_code" rows="5" class="form-control" placeholder="&lt;script&gt;(function(w,d,s,l,i)...&lt;/script&gt;" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ $settings['google_tag_manager_code'] }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="microsoft_clarity_code" style="display: block; font-weight: bold; margin-bottom: 5px;">Microsoft Clarity Script Code</label>
            <textarea id="microsoft_clarity_code" name="microsoft_clarity_code" rows="5" class="form-control" placeholder="&lt;script type='text/javascript'&gt;(function(c,l,a,r,i,t,y)...&lt;/script&gt;" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ $settings['microsoft_clarity_code'] }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="facebook_pixel_code" style="display: block; font-weight: bold; margin-bottom: 5px;">Facebook Pixel Script Code</label>
            <textarea id="facebook_pixel_code" name="facebook_pixel_code" rows="5" class="form-control" placeholder="&lt;script&gt;!function(f,b,e,v,n,t,s)...&lt;/script&gt;" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ $settings['facebook_pixel_code'] }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="custom_header_code" style="display: block; font-weight: bold; margin-bottom: 5px;">Custom Header Scripts (General)</label>
            <textarea id="custom_header_code" name="custom_header_code" rows="5" class="form-control" placeholder="Paste any header script tags here..." style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ $settings['custom_header_code'] }}</textarea>
        </div>

        <button type="submit" class="btn primary">Save Ad Settings</button>
    </form>
</div>
@endsection
