@extends('admin.layout')

@section('content')
<div class="admin-seo-edit">
    <div class="admin-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <span class="kicker">SEO MANAGER</span>
            <h2>Edit SEO Settings</h2>
            <p>Target: <strong>{{ $title }}</strong></p>
            <p style="font-size: 13px; margin-top: 4px;">Live Path: <a href="{{ $url }}" target="_blank" style="color: #c79a2b; font-family: monospace;">{{ $url }}</a></p>
        </div>
        <a href="{{ route('admin.seo.index') }}" class="btn secondary" style="text-decoration: none;">&larr; Back to Inventory</a>
    </div>

    <form method="POST" action="{{ route('admin.seo.update') }}" enctype="multipart/form-data" class="panel" style="padding: 24px;">
        @csrf
        
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="id" value="{{ $id }}">
        <input type="hidden" name="path" value="{{ $path }}">

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Left Column: Fields -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Section 1: General Meta -->
                <div style="border-bottom: 1px solid rgba(199,154,43,0.15); padding-bottom: 20px;">
                    <h3 style="color: #c79a2b; font-family: Georgia, serif; margin-bottom: 14px;">1. General Search Meta</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 6px;">SEO Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}" placeholder="Override title tag in head..." style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff;">
                            <small style="opacity: 0.6; display: block; margin-top: 4px;">Recommended length: 50-60 characters.</small>
                        </div>

                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Meta Description</label>
                            <textarea name="meta_description" rows="4" placeholder="Override description tag in head..." style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff;">{{ old('meta_description', $seo->meta_description) }}</textarea>
                            <small style="opacity: 0.6; display: block; margin-top: 4px;">Recommended length: 150-160 characters to avoid snippet truncation.</small>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Meta Keywords (Optional)</label>
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords) }}" placeholder="comma-separated tags..." style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Robots Directives</label>
                                <select name="robots_meta" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff;">
                                    <option value="index,follow" @selected(old('robots_meta', $seo->robots_meta) === 'index,follow')>Index, Follow (Default)</option>
                                    <option value="noindex,follow" @selected(old('robots_meta', $seo->robots_meta) === 'noindex,follow')>Noindex, Follow</option>
                                    <option value="noindex,nofollow" @selected(old('robots_meta', $seo->robots_meta) === 'noindex,nofollow')>Noindex, Nofollow</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Canonical URL</label>
                            <input type="url" name="canonical_url" value="{{ old('canonical_url', $seo->canonical_url) }}" placeholder="Leave blank to use current URL..." style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff;">
                            <small style="opacity: 0.6; display: block; margin-top: 4px;">Points to original source if this page is syndicated or duplicated.</small>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Social Meta -->
                <div style="border-bottom: 1px solid rgba(199,154,43,0.15); padding-bottom: 20px;">
                    <h3 style="color: #c79a2b; font-family: Georgia, serif; margin-bottom: 14px;">2. Social & Rich Previews</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- Open Graph (Facebook/LinkedIn) -->
                        <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 6px; border: 1px solid rgba(199,154,43,0.1);">
                            <h4 style="margin-bottom: 12px; color: #efe5d1;">Open Graph Meta</h4>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">OG Title</label>
                                    <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title) }}" placeholder="Falls back to SEO title if blank..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.2); background: rgba(0,0,0,0.1); color: #fff;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">OG Description</label>
                                    <textarea name="og_description" rows="2" placeholder="Falls back to SEO description if blank..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.2); background: rgba(0,0,0,0.1); color: #fff;">{{ old('og_description', $seo->og_description) }}</textarea>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">OG Image (Upload)</label>
                                    <input type="file" name="og_image" style="width: 100%; padding: 6px; border: 1px solid rgba(199,154,43,0.2); border-radius: 4px; background: rgba(0,0,0,0.1); color: #fff;">
                                    @if($seo->og_image)
                                        <div style="margin-top: 8px;">
                                            <span style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 4px;">Current OG Image:</span>
                                            <img src="{{ asset($seo->og_image) }}" alt="OG Preview" style="max-height: 80px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.2);">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Cards -->
                        <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 6px; border: 1px solid rgba(199,154,43,0.1);">
                            <h4 style="margin-bottom: 12px; color: #efe5d1;">Twitter Cards</h4>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Twitter Card Title</label>
                                    <input type="text" name="twitter_title" value="{{ old('twitter_title', $seo->schema_data['twitter_title'] ?? '') }}" placeholder="Falls back to OG title if blank..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.2); background: rgba(0,0,0,0.1); color: #fff;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Twitter Card Description</label>
                                    <textarea name="twitter_description" rows="2" placeholder="Falls back to OG description if blank..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.2); background: rgba(0,0,0,0.1); color: #fff;">{{ old('twitter_description', $seo->schema_data['twitter_description'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Twitter Image (Upload)</label>
                                    <input type="file" name="twitter_image" style="width: 100%; padding: 6px; border: 1px solid rgba(199,154,43,0.2); border-radius: 4px; background: rgba(0,0,0,0.1); color: #fff;">
                                    @if(isset($seo->schema_data['twitter_image']))
                                        <div style="margin-top: 8px;">
                                            <span style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 4px;">Current Twitter Image:</span>
                                            <img src="{{ asset($seo->schema_data['twitter_image']) }}" alt="Twitter Preview" style="max-height: 80px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.2);">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Section 3: Schema Markup -->
                <div>
                    <h3 style="color: #c79a2b; font-family: Georgia, serif; margin-bottom: 14px;">3. Structured Schema Markup</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Schema Type</label>
                            <select name="schema_type" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff;">
                                <option value="None" @selected(old('schema_type', $seo->schema_type) === 'None')>None</option>
                                <option value="Article" @selected(old('schema_type', $seo->schema_type) === 'Article')>Article</option>
                                <option value="NewsArticle" @selected(old('schema_type', $seo->schema_type) === 'NewsArticle')>NewsArticle</option>
                                <option value="BlogPosting" @selected(old('schema_type', $seo->schema_type) === 'BlogPosting')>BlogPosting</option>
                                <option value="FAQ" @selected(old('schema_type', $seo->schema_type) === 'FAQ')>FAQ Page Schema</option>
                                <option value="Breadcrumb" @selected(old('schema_type', $seo->schema_type) === 'Breadcrumb')>Breadcrumb Schema</option>
                                <option value="Organization" @selected(old('schema_type', $seo->schema_type) === 'Organization')>Organization Schema</option>
                                <option value="NewsMediaOrganization" @selected(old('schema_type', $seo->schema_type) === 'NewsMediaOrganization')>NewsMediaOrganization Schema</option>
                                <option value="WebPage" @selected(old('schema_type', $seo->schema_type) === 'WebPage')>WebPage Schema</option>
                                <option value="CollectionPage" @selected(old('schema_type', $seo->schema_type) === 'CollectionPage')>CollectionPage Schema</option>
                                <option value="Custom" @selected(old('schema_type', $seo->schema_type) === 'Custom' || old('schema_type', $seo->schema_type) === 'Custom JSON-LD')>Custom JSON-LD Schema</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Custom JSON-LD Input</label>
                            <textarea name="custom_schema" rows="6" placeholder="Paste custom script tags payload here (e.g. { '@@context': ... })" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(199,154,43,0.3); background: rgba(0,0,0,0.1); color: #fff; font-family: monospace; font-size: 13px;">{{ old('custom_schema', $seo->schema_data['custom_schema'] ?? '') }}</textarea>
                            <small style="opacity: 0.6; display: block; margin-top: 4px;">Only outputs when 'Custom JSON-LD Schema' is selected above.</small>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Instructions/Status -->
            <div>
                <div style="background: rgba(255, 255, 255, 0.03); padding: 20px; border-radius: 8px; border: 1px solid rgba(199,154,43,0.2); position: sticky; top: 20px;">
                    <h4 style="color: #c79a2b; margin-bottom: 12px; font-family: Georgia, serif;">SEO Scorecard & Guidance</h4>
                    <ul style="padding-left: 20px; margin: 0; line-height: 1.6; font-size: 13px; display: grid; gap: 8px;">
                        <li><strong>Title Tag:</strong> Keep it under 60 characters. Always include your brand suffix `| MILLENNIUM NEWSROOM` if not overridden.</li>
                        <li><strong>Meta Description:</strong> Write an active, compelling summary of 140-160 characters detailing value.</li>
                        <li><strong>Canonical Link:</strong> Only declare this explicitly to handle cross-domain posts or duplicate URLs.</li>
                        <li><strong>Schema:</strong> Rich structured metadata helps crawlability and results in Google rich search result cards.</li>
                    </ul>
                    
                    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(199,154,43,0.15);">
                        <button type="submit" class="btn primary" style="width: 100%; padding: 12px; font-weight: bold;">Save SEO Configuration</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
