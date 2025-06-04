
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            branding: false,
            plugins: 'preview anchor autolink charmap codesample emoticons image link lists  searchreplace table visualblocks wordcount fullscreen searchreplace',
            toolbar: [
                'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align lineheight ',
                'numlist bullist indent outdent | link image table| emoticons charmap removeformat|fullscreen|searchreplace',
            ],
            height: 500,
            setup: function(editor) {
                // Add the custom preview button
                // editor.ui.registry.addButton('customPreview', {
                //     text: 'Preview',
                //     icon: 'preview',
                //     onAction: function () {
                //         editor.execCommand('mcePreview');
                //     }
                // });

                editor.on('init', function() {
                    // Add the custom preview button to the far right
                    setTimeout(function() {
                        // Create a custom floating button
                        const editorContainer = editor.getContainer();
                        if (!editorContainer) return; // Guard against null container
                        const customBtn = document.createElement('button');
                        customBtn.className = 'custom-preview-btn';
                        customBtn.innerHTML = 'Preview';
                        customBtn.style.position = 'absolute';
                        customBtn.style.right = '10px';
                        customBtn.style.top = '5px';
                        customBtn.style.zIndex = '10';
                        customBtn.style.backgroundColor = '#229799';
                        customBtn.style.color = 'white';
                        customBtn.style.border = 'none';
                        customBtn.style.borderRadius = '4px';
                        customBtn.style.padding = '6px 12px';
                        customBtn.style.display = 'flex';
                        customBtn.style.alignItems = 'center';
                        customBtn.style.gap = '5px';
                        customBtn.style.cursor = 'pointer';
                        customBtn.style.fontWeight = '500';
                        customBtn.style.fontSize = '14px';
                        
                        // Add hover effect
                        customBtn.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#48CFCB';
                        });
                        customBtn.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '#229799';
                        });
                        
                        // Add click action
                        customBtn.addEventListener('click', function() {
                            editor.execCommand('mcePreview');
                        });
                        
                        
                        // Add to editor
                        editorContainer.appendChild(customBtn);
                    }, 200);
                });
            },
            automatic_uploads: true,
            file_picker_types: 'image',
            images_reuse_filename: true,
            images_upload_handler: function (blobInfo, progress) { // Changed signature
                return new Promise((resolve, reject) => {
                    // Ensure the global config object and its properties exist
                    if (!window.tinyMCEUploadConfig || !window.tinyMCEUploadConfig.uploadUrl || !window.tinyMCEUploadConfig.csrfToken) {
                        reject({ message: 'TinyMCE upload configuration (URL or CSRF token) is missing.', remove: true });
                        return;
                    }

                    var xhr, formData;
                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = false; 
                    xhr.open('POST', window.tinyMCEUploadConfig.uploadUrl);
                    xhr.setRequestHeader("X-CSRF-Token", window.tinyMCEUploadConfig.csrfToken);
                    xhr.setRequestHeader('Accept', 'application/json'); // Good practice to set Accept header

                    xhr.upload.onprogress = function (e) {
                        if (progress && typeof progress === 'function') {
                           progress(e.loaded / e.total * 100);
                        }
                    };

                    xhr.onload = function() {
                        if (xhr.status < 200 || xhr.status >= 300) {
                            let errorMessage = 'HTTP Error: ' + xhr.status;
                            try {
                                const errorResponse = JSON.parse(xhr.responseText);
                                if (errorResponse && (errorResponse.message || errorResponse.error)) {
                                    errorMessage = errorResponse.message || errorResponse.error;
                                }
                            } catch (e) { /* Ignore parsing error, use default HTTP error */ }
                            reject({ message: errorMessage, remove: true });
                            return;
                        }
                        
                        var json;
                        try {
                            json = JSON.parse(xhr.responseText);
                        } catch (e) {
                            reject({ message: 'Invalid JSON response from server: ' + xhr.responseText, remove: true });
                            return;
                        }

                        if (!json || typeof json.location != 'string') {
                            reject({ message: 'Invalid JSON response from server: missing location property.', remove: true });
                            return;
                        }
                        resolve(json.location); // Resolve the promise with the image location
                    };

                    xhr.onerror = function () {
                        reject({ message: 'Image upload failed due to a network error. Please check your connection.', remove: true });
                    };

                    formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                });
            }
        });

// // Initialize TinyMCE
        // tinymce.init({
        //     selector: '#content',
        //     branding: false,
        //     plugins: 'preview anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount|searchreplace',
        //     // Remove preview from the toolbar
        //     toolbar: [
        //         'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align lineheight ',
        //         ' numlist bullist indent outdent | link image table| emoticons charmap removeformat|searchreplace',
        //     ],
        //     height: 500,
        //     // Add custom preview button
        //     setup: function(editor) {
        //         // Add the custom preview button
        //         // editor.ui.registry.addButton('customPreview', {
        //         //     text: 'Preview',
        //         //     icon: 'preview',
        //         //     onAction: function () {
        //         //         editor.execCommand('mcePreview');
        //         //     }
        //         // });

        //         editor.on('init', function() {
        //             // Add the custom preview button to the far right
        //             setTimeout(function() {
        //                 // Create a custom floating button
        //                 const editorContainer = editor.getContainer();
        //                 const customBtn = document.createElement('button');
        //                 customBtn.className = 'custom-preview-btn';
        //                 customBtn.innerHTML = 'Preview';
        //                 customBtn.style.position = 'absolute';
        //                 customBtn.style.right = '10px';
        //                 customBtn.style.top = '5px';
        //                 customBtn.style.zIndex = '10';
        //                 customBtn.style.backgroundColor = '#229799';
        //                 customBtn.style.color = 'white';
        //                 customBtn.style.border = 'none';
        //                 customBtn.style.borderRadius = '4px';
        //                 customBtn.style.padding = '6px 12px';
        //                 customBtn.style.display = 'flex';
        //                 customBtn.style.alignItems = 'center';
        //                 customBtn.style.gap = '5px';
        //                 customBtn.style.cursor = 'pointer';
        //                 customBtn.style.fontWeight = '500';
        //                 customBtn.style.fontSize = '14px';
                        
        //                 // Add hover effect
        //                 customBtn.addEventListener('mouseenter', function() {
        //                     this.style.backgroundColor = '#48CFCB';
        //                 });
        //                 customBtn.addEventListener('mouseleave', function() {
        //                     this.style.backgroundColor = '#229799';
        //                 });
                        
        //                 // Add click action
        //                 customBtn.addEventListener('click', function() {
        //                     editor.execCommand('mcePreview');
        //                 });
                        
                        
        //                 // Add to editor
        //                 editorContainer.appendChild(customBtn);
        //             }, 200);
        //         });
        //     },
        //     // // Rest of your configuration 
        //     // images_upload_url: '{{ route("articles.upload-image") }}',
        //     // automatic_uploads: true,
        //     // file_picker_types: 'image',
        //     // images_reuse_filename: true,
        //     // images_upload_handler: function (blobInfo, success, failure) {
        //     //     var xhr, formData;
        //     //     xhr = new XMLHttpRequest();
        //     //     xhr.withCredentials = false;
        //     //     xhr.open('POST', '{{ route("articles.upload-image") }}');
        //     //     var token = '{{ csrf_token() }}';
        //     //     xhr.setRequestHeader("X-CSRF-Token", token);
        //     //     xhr.onload = function() {
        //     //         var json;
        //     //         if (xhr.status != 200) {
        //     //             failure('HTTP Error: ' + xhr.status);
        //     //             return;
        //     //         }
        //     //         json = JSON.parse(xhr.responseText);
        //     //         if (!json || typeof json.location != 'string') {
        //     //             failure('Invalid JSON: ' + xhr.responseText);
        //     //             return;
        //     //         }
        //     //         success(json.location);
        //     //     };
        //     //     formData = new FormData();
        //     //     formData.append('file', blobInfo.blob(), blobInfo.filename());
        //     //     xhr.send(formData);
        //     // }
        // });

        // // Image preview functionality
        // document.getElementById('featured_image').onchange = function(e) {
        //     const preview = document.getElementById('image-preview');
        //     const container = document.querySelector('.preview-container');
        //     const file = e.target.files[0];
            
        //     if (file) {
        //         preview.src = URL.createObjectURL(file);
        //         container.classList.remove('d-none');
        //     } else {
        //         preview.src = '#';
        //         container.classList.add('d-none');
        //     }
        // };

        // document.getElementById('remove-image').onclick = function() {
        //     const input = document.getElementById('featured_image');
        //     const container = document.querySelector('.preview-container');
        //     input.value = '';
        //     container.classList.add('d-none');
        // };
