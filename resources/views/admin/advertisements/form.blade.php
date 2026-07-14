@extends('admin.layout')
@section('content')
<div class="admin-title">
    <div>
        <span class="kicker">Advertisements</span>
        <h2>{{ $ad->exists ? 'Edit Advertisement' : 'New Advertisement' }}</h2>
    </div>
</div>

<div class="panel">
    <form method="POST" action="{{ $ad->exists ? route('admin.advertisements.update', $ad) : route('admin.advertisements.store') }}" enctype="multipart/form-data">
        @csrf
        @if($ad->exists)
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="name" style="display: block; font-weight: bold; margin-bottom: 5px;">Advertisement Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $ad->name) }}" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
            
            <div class="form-group">
                <label for="placement" style="display: block; font-weight: bold; margin-bottom: 5px;">Placement Area *</label>
                <select id="placement" name="placement" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
                    @foreach($placements as $key => $label)
                        <option value="{{ $key }}" {{ old('placement', $ad->placement) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="type" style="display: block; font-weight: bold; margin-bottom: 5px;">Advertisement Type *</label>
                <select id="type" name="type" required onchange="toggleTypeFields()" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
                    <option value="google_adsense" {{ old('type', $ad->type) === 'google_adsense' ? 'selected' : '' }}>Google AdSense</option>
                    <option value="google_ad_manager" {{ old('type', $ad->type) === 'google_ad_manager' ? 'selected' : '' }}>Google Ad Manager</option>
                    <option value="media_net" {{ old('type', $ad->type) === 'media_net' ? 'selected' : '' }}>Media.net</option>
                    <option value="html" {{ old('type', $ad->type) === 'html' ? 'selected' : '' }}>Custom HTML</option>
                    <option value="js" {{ old('type', $ad->type) === 'js' ? 'selected' : '' }}>JavaScript Code</option>
                    <option value="affiliate" {{ old('type', $ad->type) === 'affiliate' ? 'selected' : '' }}>Affiliate Banner</option>
                    <option value="image" {{ old('type', $ad->type) === 'image' ? 'selected' : '' }}>Image Banner</option>
                    <option value="iframe" {{ old('type', $ad->type) === 'iframe' ? 'selected' : '' }}>Iframe</option>
                </select>
            </div>

            <div class="form-group">
                <label for="device" style="display: block; font-weight: bold; margin-bottom: 5px;">Device Compatibility *</label>
                <select id="device" name="device" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
                    <option value="all" {{ old('device', $ad->device) === 'all' ? 'selected' : '' }}>All Devices</option>
                    <option value="desktop" {{ old('device', $ad->device) === 'desktop' ? 'selected' : '' }}>Desktop Only</option>
                    <option value="tablet" {{ old('device', $ad->device) === 'tablet' ? 'selected' : '' }}>Tablet Only</option>
                    <option value="mobile" {{ old('device', $ad->device) === 'mobile' ? 'selected' : '' }}>Mobile Only</option>
                </select>
            </div>

            <div class="form-group">
                <label for="priority" style="display: block; font-weight: bold; margin-bottom: 5px;">Position Priority (Higher First) *</label>
                <input type="number" id="priority" name="priority" value="{{ old('priority', $ad->priority ?? 0) }}" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
        </div>

        <!-- Code section (for Adsense, HTML, JS) -->
        <div id="code_fields" style="margin-bottom: 20px;">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="code" style="display: block; font-weight: bold; margin-bottom: 5px;">Advertisement Script/HTML Code (Default)</label>
                <textarea id="code" name="code" rows="4" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ old('code', $ad->code) }}</textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="code_desktop" style="display: block; font-weight: bold; margin-bottom: 5px;">Code (Desktop Override)</label>
                    <textarea id="code_desktop" name="code_desktop" rows="3" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ old('code_desktop', $ad->code_desktop) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="code_tablet" style="display: block; font-weight: bold; margin-bottom: 5px;">Code (Tablet Override)</label>
                    <textarea id="code_tablet" name="code_tablet" rows="3" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ old('code_tablet', $ad->code_tablet) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="code_mobile" style="display: block; font-weight: bold; margin-bottom: 5px;">Code (Mobile Override)</label>
                    <textarea id="code_mobile" name="code_mobile" rows="3" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: monospace;">{{ old('code_mobile', $ad->code_mobile) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Image fields (for Image Banners and Affiliates) -->
        <div id="image_fields" style="margin-bottom: 20px; display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="image" style="display: block; font-weight: bold; margin-bottom: 5px;">Default Banner Image</label>
                    <input type="file" id="image" name="image" style="width: 100%;">
                    @if($ad->image)
                        <div style="margin-top: 10px;"><a href="{{ asset('storage/' . $ad->image) }}" target="_blank" style="color: #c79a2b;">View current image</a></div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="image_tablet" style="display: block; font-weight: bold; margin-bottom: 5px;">Tablet Banner Image</label>
                    <input type="file" id="image_tablet" name="image_tablet" style="width: 100%;">
                    @if($ad->image_tablet)
                        <div style="margin-top: 10px;"><a href="{{ asset('storage/' . $ad->image_tablet) }}" target="_blank" style="color: #c79a2b;">View current image</a></div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="image_mobile" style="display: block; font-weight: bold; margin-bottom: 5px;">Mobile Banner Image</label>
                    <input type="file" id="image_mobile" name="image_mobile" style="width: 100%;">
                    @if($ad->image_mobile)
                        <div style="margin-top: 10px;"><a href="{{ asset('storage/' . $ad->image_mobile) }}" target="_blank" style="color: #c79a2b;">View current image</a></div>
                    @endif
                </div>
            </div>
            
            <div class="form-group">
                <label for="target_url" style="display: block; font-weight: bold; margin-bottom: 5px;">Target / Redirection URL</label>
                <input type="url" id="target_url" name="target_url" value="{{ old('target_url', $ad->target_url) }}" placeholder="https://example.com/promo" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="start_date" style="display: block; font-weight: bold; margin-bottom: 5px;">Start Date</label>
                <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date', $ad->start_date ? $ad->start_date->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
            
            <div class="form-group">
                <label for="end_date" style="display: block; font-weight: bold; margin-bottom: 5px;">End Date</label>
                <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date', $ad->end_date ? $ad->end_date->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="max_views" style="display: block; font-weight: bold; margin-bottom: 5px;">Max Views Limit</label>
                <input type="number" id="max_views" name="max_views" value="{{ old('max_views', $ad->max_views) }}" placeholder="Unlimited" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
            
            <div class="form-group">
                <label for="max_clicks" style="display: block; font-weight: bold; margin-bottom: 5px;">Max Clicks Limit</label>
                <input type="number" id="max_clicks" name="max_clicks" value="{{ old('max_clicks', $ad->max_clicks) }}" placeholder="Unlimited" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>

            <div class="form-group">
                <label for="width" style="display: block; font-weight: bold; margin-bottom: 5px;">Placeholder Width (px)</label>
                <input type="number" id="width" name="width" value="{{ old('width', $ad->width) }}" placeholder="Auto" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
            
            <div class="form-group">
                <label for="height" style="display: block; font-weight: bold; margin-bottom: 5px;">Placeholder Height (px)</label>
                <input type="number" id="height" name="height" value="{{ old('height', $ad->height) }}" placeholder="Auto" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 10px;">Target Pages Visibility (Show everywhere if none selected)</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px;">
                @foreach(['homepage' => 'Homepage', 'category' => 'Category Pages', 'single' => 'Single Post Pages', 'search' => 'Search Page', 'author' => 'Author Page', 'tag' => 'Tag Page', 'static' => 'Static Pages'] as $key => $label)
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="target_pages[]" value="{{ $key }}" {{ is_array(old('target_pages', $ad->target_pages ?? [])) && in_array($key, old('target_pages', $ad->target_pages ?? [])) ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="is_active" style="display: block; font-weight: bold; margin-bottom: 5px;">Status *</label>
            <select id="is_active" name="is_active" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff;">
                <option value="1" {{ old('is_active', $ad->is_active ?? 1) == 1 ? 'selected' : '' }}>Active / Serving</option>
                <option value="0" {{ old('is_active', $ad->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive / Paused</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn primary">Save Advertisement</button>
            <a class="btn" href="{{ route('admin.advertisements.index') }}">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleTypeFields() {
    const type = document.getElementById('type').value;
    const codeFields = document.getElementById('code_fields');
    const imageFields = document.getElementById('image_fields');
    
    if (type === 'image' || type === 'affiliate') {
        codeFields.style.display = 'none';
        imageFields.style.display = 'block';
    } else {
        codeFields.style.display = 'block';
        imageFields.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', toggleTypeFields);
</script>
@endsection
