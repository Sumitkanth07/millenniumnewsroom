@extends('admin.layout')

@section('content')
<h2>{{ $redirect->exists ? 'Edit Redirect' : 'Create Redirect' }}</h2>
<form class="panel form" method="POST" action="{{ $redirect->exists ? route('admin.redirects.update', $redirect) : route('admin.redirects.store') }}">
    @csrf @if($redirect->exists) @method('PUT') @endif
    <label>Source path <input name="source" value="{{ old('source', $redirect->source) }}" placeholder="/old-page" required></label>
    <label>Destination <input name="destination" value="{{ old('destination', $redirect->destination) }}" placeholder="/new-page" required></label>
    <label>Status code
        <select name="status_code">
            <option value="301" @selected(old('status_code', $redirect->status_code ?? 301) == 301)>301 Permanent</option>
            <option value="302" @selected(old('status_code', $redirect->status_code) == 302)>302 Temporary</option>
        </select>
    </label>
    <label class="check"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $redirect->is_active ?? true))> Active</label>
    <button class="btn primary">Save Redirect</button>
</form>
@endsection
