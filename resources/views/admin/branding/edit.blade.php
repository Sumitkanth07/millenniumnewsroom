@extends('admin.layout')

@section('content')

<h2>Branding</h2>

<form
    class="panel form"
    method="POST"
    action="{{ route('admin.branding.update') }}"
    enctype="multipart/form-data"
>

    @csrf
    @method('PUT')

    <label>

        Site name

        <input
            name="site_name"
            value="{{ old('site_name', $siteName) }}"
        >

    </label>

    <label>

        Site title

        <input
            name="site_title"
            value="{{ old('site_title', $siteTitle) }}"
        >

    </label>

    <label>

        Tagline

        <input
            name="tagline"
            value="{{ old('tagline', $tagline) }}"
        >

    </label>

    <label>

        Primary color

        <input
            name="primary_color"
            type="color"
            value="{{ $primaryColor }}"
        >

    </label>

    <label>

        Secondary color

        <input
            name="secondary_color"
            type="color"
            value="{{ $secondaryColor }}"
        >

    </label>

    @if($logo)

        <div class="preview-box">

            <span>Current logo</span>

            <img
                src="{{ url($logo) }}"
                alt="{{ $siteName }} logo"
                style="
                    max-width:220px;
                    height:auto;
                    display:block;
                    margin-top:12px;
                "
            >

            <small>

                Saved as {{ $logo }}

            </small>

        </div>

    @endif

    <label>

        Logo

        <input
            name="logo"
            type="file"
            accept="image/*"
        >

    </label>

    <label>

        Meta description

        <textarea
            name="meta_description"
            rows="4"
        >{{ old('meta_description', \App\Models\Setting::getValue('meta_description')) }}</textarea>

    </label>

    <button class="btn primary">

        Save Branding

    </button>

</form>

@endsection