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
            'link image media table | ' +
            'codesample blockquote | ' +
            'preview fullscreen code',

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
