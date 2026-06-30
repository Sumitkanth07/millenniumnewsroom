@extends('admin.layout')

@section('content')
<div class="admin-title">
    <div><span class="kicker">Newsroom CMS</span><h2>{{ $blog->exists ? 'Edit Post' : 'Create Post' }}</h2></div>
    @if($blog->exists)<a class="btn" href="{{ $blog->publicUrl() }}" target="_blank">Live Preview</a>@endif
</div>
<form class="admin-form-grid" method="POST" action="{{ $blog->exists ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" enctype="multipart/form-data">
    @csrf @if($blog->exists) @method('PUT') @endif

    <section class="panel form">
        <label>Headline <input id="titleInput" name="title" value="{{ old('title', $blog->title) }}" required></label>
        <label>Slug <input id="slugInput" name="slug" value="{{ old('slug', $blog->slug) }}" placeholder="auto-generated-from-headline"></label>
        <label>Short description <textarea name="excerpt" rows="3">{{ old('excerpt', $blog->excerpt) }}</textarea></label>
        <label>Full content <textarea id="content" name="content" rows="16">{{ old('content', $blog->content) }}</textarea></label>
        <label>Tags <input name="tags" value="{{ old('tags', $tagList ?: $blog->tags_cache) }}" placeholder="AI, Media, Technology"></label>
    </section>

    <aside class="panel form">
        @if($blog->exists)
            <div style="background: var(--surface-soft, rgba(0,0,0,0.02)); padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid var(--line, #ebedf2); font-size: 13px;">
                <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--secondaryColor, #c79a2b);">Post Analytics</h4>
                <div style="display: grid; gap: 6px; line-height: 1.5;">
                    <div><strong>Total Views:</strong> {{ number_format($blog->views_count) }}</div>
                    <div><strong>Publish Date:</strong> {{ $blog->published_at ? $blog->published_at->format('d M Y, H:i') : 'Not published' }}</div>
                    <div><strong>Last Updated:</strong> {{ $blog->updated_at ? $blog->updated_at->format('d M Y, H:i') : 'Never' }}</div>
                    @if($blog->comments()->exists())
                        <div><strong>Total Comments:</strong> {{ number_format($blog->comments()->count()) }}</div>
                    @endif
                </div>
                <div style="margin-top: 12px; border-top: 1px dashed var(--line, #ebedf2); padding-top: 10px;">
                    <button type="submit" form="resetViewsForm" class="btn small" style="background-color: var(--secondaryColor, #c79a2b) !important; color: #fff !important; width: auto; padding: 6px 12px; font-size: 11px; margin: 0;">Reset Views</button>
                </div>
            </div>
        @endif

        <h3>Publishing</h3>
        <label>Status
            <select name="status">
                @foreach(['draft' => 'Draft', 'published' => 'Published', 'scheduled' => 'Scheduled'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $blog->status ?? 'draft') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <div class="scheduler-box">
            <label>Date <input name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at', optional($blog->scheduled_at)->format('Y-m-d\TH:i')) }}"></label>
        </div>
        <label class="check"><input name="is_published" type="checkbox" value="1" @checked(old('is_published', $blog->is_published))> Publish now</label>
        <label class="check"><input name="is_featured" type="checkbox" value="1" @checked(old('is_featured', $blog->is_featured))> Featured story</label>
        <label class="check"><input name="is_breaking" type="checkbox" value="1" @checked(old('is_breaking', $blog->is_breaking))> Breaking news</label>
        <label class="check"><input name="is_trending" type="checkbox" value="1" @checked(old('is_trending', $blog->is_trending))> Force trending</label>

        <h3>Category & Author</h3>
        <label>Category
            <select name="category_id">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $blog->category_id) == $category->id)>{{ $category->parent ? $category->parent->name.' / ' : '' }}{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <div class="quick-create">
            <label>Quick add category <input name="new_category_name" placeholder="New category name"></label>
            <label>Parent category
                <select name="new_category_parent_id"><option value="">No parent</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
            </label>
        </div>
        <label>Author
            <select name="author_id">
                <option value="">Staff desk</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" @selected(old('author_id', $blog->author_id) == $author->id)>{{ $author->name }}</option>
                @endforeach
            </select>
        </label>
    </aside>

   <section class="panel form full-width">
    <h3>Featured Image SEO</h3>

    @if($blog->featured_image || $blog->image)
        <div class="preview-box">
            <span>Current image</span>

            <img 
                src="{{ asset($blog->featured_image ?: $blog->image) }}" 
                alt="{{ $blog->featured_image_alt ?: $blog->title }}">

            <small>
                {{ basename($blog->featured_image ?: $blog->image) }}
            </small>
        </div>
    @endif

    <label>
        Featured image
        <input id="featuredImageInput" name="featured_image" type="file" accept="image/*">
    </label>

    <div id="featuredImagePreview" class="preview-box" hidden>
        <span>New image preview</span>
        <img alt="">
        <small></small>
    </div>

    <label>
        Gallery images
        <input name="gallery_images[]" type="file" accept="image/*" multiple>
    </label>

    @if($blog->gallery_images)

    <div class="gallery-admin-grid">

        @foreach($blog->gallery_images as $image)

            <figure class="gallery-item">

                <img
                    src="{{ asset($image) }}"
                    alt="{{ basename($image) }}"
                >

                <figcaption>

                    {{ basename($image) }}

                    <small>{{ $image }}</small>

                </figcaption>

                <label
                    style="
                        display:block;
                        margin-top:10px;
                        color:#fff;
                        font-size:14px;
                    "
                >

                    <input
                        type="checkbox"
                        name="remove_gallery_images[]"
                        value="{{ $image }}"
                    >

                    Remove image

                </label>

            </figure>

        @endforeach

    </div>

@endif
    <label>
        Alt text
        <input name="featured_image_alt" value="{{ old('featured_image_alt', $blog->featured_image_alt) }}">
    </label>

    <label>
        Title text
        <input name="featured_image_title" value="{{ old('featured_image_title', $blog->featured_image_title) }}">
    </label>

    <label>
        Caption
        <input name="featured_image_caption" value="{{ old('featured_image_caption', $blog->featured_image_caption) }}">
    </label>

    <label>
        Description
        <textarea name="featured_image_description" rows="3">{{ old('featured_image_description', $blog->featured_image_description) }}</textarea>
    </label>
</section>

    <section class="panel form full-width">
        <h3>SEO Preview</h3>
        <div class="seo-preview"><strong>{{ old('meta_title', $blog->meta_title ?: $blog->title ?: 'SEO title preview') }}</strong><span>{{ old('canonical_url', $blog->canonical_url ?: ($blog->exists ? $blog->publicUrl() : url('/category-slug/'.($blog->slug ?: 'post-slug')))) }}</span><p>{{ old('meta_description', $blog->meta_description ?: $blog->excerpt ?: 'Meta description preview appears here.') }}</p></div>
        <label>Meta title <input name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}"></label>
        <label>Meta description <textarea name="meta_description" rows="3">{{ old('meta_description', $blog->meta_description) }}</textarea></label>
        <label>Meta keywords <input name="meta_keywords" value="{{ old('meta_keywords', $blog->meta_keywords) }}"></label>
        <label>Canonical URL <input name="canonical_url" value="{{ old('canonical_url', $blog->canonical_url) }}"></label>
        <label>Robots meta <input name="robots_meta" value="{{ old('robots_meta', $blog->robots_meta ?: 'index,follow') }}"></label>
    </section>

    <button class="btn primary save-post">Save Post</button>
</form>

@if($blog->exists)
    <form id="resetViewsForm" method="POST" action="{{ route('admin.blogs.reset-views', $blog) }}" onsubmit="return confirm('Are you sure you want to reset views for this post?');" style="display: none;">
        @csrf
    </form>
@endif
@include('admin.partials.tinymce')
@push('scripts')
<script>
const titleInput = document.getElementById('titleInput');
const slugInput = document.getElementById('slugInput');
titleInput?.addEventListener('input', () => {
    if (!slugInput.dataset.touched && !slugInput.value) {
        slugInput.value = titleInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});
slugInput?.addEventListener('input', () => slugInput.dataset.touched = '1');
document.getElementById('featuredImageInput')?.addEventListener('change', (event) => {
    const file = event.target.files[0];
    const box = document.getElementById('featuredImagePreview');
    if (!file || !box) return;
    box.hidden = false;
    box.querySelector('img').src = URL.createObjectURL(file);
    box.querySelector('small').textContent = file.name;
});
</script>
@endpush
@endsection
