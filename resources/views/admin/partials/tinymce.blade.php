@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

<script>

window.addEventListener('load', () => {

    if (!window.tinymce) {
        return;
    }

    const isDark = (localStorage.getItem('millennium-admin-theme') || 'dark') === 'dark';

    tinymce.init({

        selector: '#content',

        height: 500,

        menubar: true,

        branding: false,
        
        skin: isDark ? 'oxide-dark' : 'oxide',
        
        content_css: isDark ? 'dark' : 'default',

        plugins:
            'image media link lists table code codesample preview wordcount autoresize fullscreen',

        toolbar:
            'undo redo | ' +
            'blocks fontfamily fontsize | ' +
            'bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | ' +
            'link image media embedmedia table | ' +
            'codesample blockquote | ' +
            'preview fullscreen code',

        extended_valid_elements: 'iframe[src|title|width|height|allow|allowfullscreen|frameborder|class|loading|style],blockquote[class|style|dir|data-*],div[class|style|id|data-*],span[class|style],a[href|target|rel|title|class],img[src|alt|title|width|height|class|loading|style]',
        valid_children: '+body[iframe|blockquote|style|script]',
        media_live_embeds: true,

        setup: (editor) => {
            editor.ui.registry.addButton('embedmedia', {
                text: 'Embed Media',
                icon: 'embed',
                tooltip: 'Insert YouTube, X/Twitter, Instagram or iframe embed',
                onAction: () => {
                    const input = prompt('Paste YouTube URL, Tweet/X URL, Instagram URL, or <iframe> embed code:');
                    if (!input || !input.trim()) return;
                    const val = input.trim();
                    
                    const ytMatch = val.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]+)/i);
                    if (ytMatch) {
                        editor.insertContent(`<div class="media-embed-responsive youtube-embed"><iframe src="https://www.youtube-nocookie.com/embed/${ytMatch[1]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe></div>`);
                        return;
                    }
                    
                    editor.insertContent(val);
                }
            });
        },

        toolbar_sticky: true,

        image_title: true,

        automatic_uploads: true,

        file_picker_types: 'image',

        images_upload_url: '{{ route('admin.upload.image') }}',

        images_upload_credentials: true,

        content_style: `
            body{
                font-family:Arial,sans-serif;
                font-size:18px;
                line-height:1.8;
                padding:20px;
            }

            img{
                max-width:100%;
                height:auto;
                border-radius:10px;
            }

            p{
                margin-bottom:16px;
            }
        `,

        images_upload_handler: (blobInfo, progress) =>
            new Promise((resolve, reject) => {

                const xhr = new XMLHttpRequest();

                xhr.open(
                    'POST',
                    '{{ route('admin.upload.image') }}'
                );

                xhr.setRequestHeader(
                    'X-CSRF-TOKEN',
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || ''
                );

                xhr.upload.onprogress = (event) => {

                    progress(
                        event.loaded / event.total * 100
                    );
                };

                xhr.onload = () => {

                    if (
                        xhr.status < 200 ||
                        xhr.status >= 300
                    ) {

                        reject(
                            'Upload failed: ' + xhr.status
                        );

                        return;
                    }

                    let json;

                    try {
                        json = JSON.parse(xhr.responseText);
                    } catch (error) {
                        reject('Upload response was not valid JSON.');
                        return;
                    }

                    if (!json.location) {
                        reject(json.message || 'Upload failed.');
                        return;
                    }

                    resolve(json.location);
                };

                xhr.onerror = () =>
                    reject('Image upload failed.');

                const formData = new FormData();

                formData.append(
                    'file',
                    blobInfo.blob(),
                    blobInfo.filename()
                );

                xhr.send(formData);
            }),

        license_key: 'gpl'

    });

});

</script>

@endpush
