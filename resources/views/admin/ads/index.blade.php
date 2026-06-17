@extends('admin.layout')
@section('content')
<div class="admin-title"><div><span class="kicker">Revenue</span><h2>Ad Management</h2></div><a class="btn primary" href="{{ route('admin.ads.create') }}">New Ad Slot</a></div>
<div class="panel">
@foreach($ads as $ad)
    <div class="row-line">
        <div><strong>{{ $ad->name }}</strong><span>{{ $ad->key }} · {{ $ad->is_active ? 'Active' : 'Hidden' }}</span></div>
        <div class="actions"><a class="btn small" href="{{ route('admin.ads.edit', $ad) }}">Edit</a><form method="POST" action="{{ route('admin.ads.destroy', $ad) }}">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form></div>
    </div>
@endforeach
</div>
@endsection
