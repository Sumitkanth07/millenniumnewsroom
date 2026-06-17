@extends('admin.layout')

@section('content')
<div class="admin-title"><div><span class="kicker">Footer Builder</span><h2>Footer Settings</h2></div></div>
<form class="panel form" method="POST" action="{{ route('admin.footer.update') }}">
    @csrf
    <label>Company name <input name="company_name" value="{{ old('company_name', $footer->company_name) }}" required></label>
    <div class="inline-fields">
        <label>Email <input name="email" type="email" value="{{ old('email', $footer->email) }}"></label>
        <label>Phone <input name="phone" value="{{ old('phone', $footer->phone) }}"></label>
    </div>
    <label>Address <textarea name="address" rows="3">{{ old('address', $footer->address) }}</textarea></label>
    <label>Copyright text <input name="copyright_text" value="{{ old('copyright_text', $footer->copyright_text) }}"></label>
    <h3>Dynamic Footer Links</h3>
    <label>Footer menus <textarea name="footer_menus" rows="4" placeholder="About Us|/page/about-us">{{ old('footer_menus', implode("\n", $footer->footer_menus ?? [])) }}</textarea></label>
    <label>Category links <textarea name="category_links" rows="4" placeholder="Markets|/category/markets">{{ old('category_links', implode("\n", $footer->category_links ?? [])) }}</textarea></label>
    <label>Social links <textarea name="social_links" rows="4" placeholder="X|https://x.com/yourprofile&#10;LinkedIn|https://linkedin.com/company/yourcompany&#10;Instagram|https://instagram.com/yourprofile&#10;YouTube|https://youtube.com/@yourchannel">{{ old('social_links', implode("\n", $footer->social_links ?? [])) }}</textarea></label>
    <label>Sitemap links <textarea name="sitemap_links" rows="4" placeholder="XML Sitemap|/sitemap.xml">{{ old('sitemap_links', implode("\n", $footer->sitemap_links ?? [])) }}</textarea></label>
    <button class="btn primary">Save Footer</button>
</form>
@endsection
