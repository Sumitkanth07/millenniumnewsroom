@extends('admin.layout')
@section('content')
<div class="admin-title"><div><span class="kicker">Google Ads</span><h2>{{ $ad->exists ? 'Edit Ad Slot' : 'Create Ad Slot' }}</h2></div></div>
<form class="panel form" method="POST" action="{{ $ad->exists ? route('admin.ads.update', $ad) : route('admin.ads.store') }}">
    @csrf @if($ad->exists) @method('PUT') @endif
    <label>Name <input name="name" value="{{ old('name', $ad->name) }}" required placeholder="Header Ad"></label>
    <label>Placement key <input name="key" value="{{ old('key', $ad->key) }}" placeholder="header_ad"></label>
    <label>AdSense / HTML code <textarea name="code" rows="10" placeholder="<script>...</script>">{{ old('code', $ad->code) }}</textarea></label>
    <label class="check"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $ad->is_active ?? true))> Active</label>
    <button class="btn primary">Save Ad Slot</button>
</form>
@endsection
