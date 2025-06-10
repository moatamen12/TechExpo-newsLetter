@extends('layouts.app')
@section('title', 'Create Newsletter')
@php
    $btn =  [
        'link' => route('dashboard.newsletter'),
        'text' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" style="margin-right: 5px;">
            <path fill="white" d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288 480 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-370.7 0 73.4-73.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-128 128z"/></svg>'
    ];

@endphp
@section('content')
<div class="container px-5 ">
    <x-dashboard-header 
        title="Create New Newsletter" 
        description="Create and send a new newsletter to subscribers"
        :btn="$btn"
        class="mb-2 ">
    </x-dashboard-header>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('articles.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <div class="mb-4 border-0 bg-white shadow-sm rounded-3">
                <div class="p-3 bg-white rounded-3">
                    <!-- Newsletter Subject -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">Newsletter Subject</label>
                        <input type="text" class="form-control form-control-sm" id="title" name="title" 
                            placeholder="Enter your newsletter subject line" minlength="10" maxlength="100"
                            value="{{ old('title') }}" required>
                        <small class="text-muted">Subject should be 10-100 characters</small>
                    </div>

                    <!-- Newsletter Preview Text -->
                    <div class="mb-4">
                        <label for="summary" class="form-label fw-bold">Preview Text</label>
                        <input type="text" class="form-control form-control-sm" id="summary" name="summary" 
                            placeholder="This text appears in email previews and helps subscribers decide to open your newsletter" 
                            minlength="30" maxlength="300" value="{{ old('summary') }}" required>
                        <small class="text-muted">Preview text should be 30-300 characters</small>
                    </div>

                    <!-- Newsletter Type (New Addition) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Newsletter Type</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="newsletter_type" id="type-weekly" 
                                        value="weekly" {{ old('newsletter_type', 'weekly') == 'weekly' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type-weekly">Weekly Digest</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="newsletter_type" id="type-special" 
                                        value="special" {{ old('newsletter_type') == 'special' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type-special">Special Edition</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="newsletter_type" id="type-announcement" 
                                        value="announcement" {{ old('newsletter_type') == 'announcement' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type-announcement">Announcement</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="">
                <!-- Content Creation Method Selection -->
                <div class="mb-4 border-0 bg-white shadow-sm rounded-3 p-3">
                    <label class="form-label fw-bold">Content Creation Method</label>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="content_method" id="method-editor" 
                                    value="editor" {{ old('content_method', 'editor') == 'editor' ? 'checked' : '' }}>
                                <label class="form-check-label" for="method-editor">
                                    <strong>Use Rich Text Editor</strong><br>
                                    <small class="text-muted">Create content using the built-in editor</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="content_method" id="method-template" 
                                    value="template" {{ old('content_method') == 'template' ? 'checked' : '' }}>
                                <label class="form-check-label" for="method-template">
                                    <strong>Upload HTML Template</strong><br>
                                    <small class="text-muted">Upload a pre-designed HTML template</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rich Text Editor Content -->
                <div class="mb-4" id="editor-content" style="display: block;">
                    <label for="content" class="form-label fw-bold">Newsletter Content</label>
                    <textarea id="content" name="content">{{ old('content') }}</textarea>
                </div>

                <!-- HTML Template Upload -->
                <div class="mb-4 border-0 bg-white shadow-sm rounded-3 p-3" id="template-content" style="display: none;">
                    <div class="mb-3">
                        <label for="html_template" class="form-label fw-bold">Upload HTML Template</label>
                        <input class="form-control" type="file" id="html_template" name="html_template" accept=".html,.htm">
                        <small class="text-muted">Upload an HTML file (.html or .htm). Max size: 2MB</small>
                    </div>
                    
                    <!-- Template Preview -->
                    <div class="mt-3" id="template-preview" style="display: none;">
                        <label class="form-label fw-bold">Template Preview</label>
                        <div class="border rounded p-2 bg-light" style="max-height: 300px; overflow-y: auto;">
                            <div id="template-preview-content"></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="remove-template">
                            Remove Template
                        </button>
                    </div>

                    <!-- Template Edit Option -->
                    <div class="mt-3" id="template-edit-option" style="display: none;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_template" name="edit_template">
                            <label class="form-check-label" for="edit_template">
                                Allow editing this template in the rich text editor
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 border border-0 rounded-3 p-3 bg-white shadow-sm">
                <div class="row py-3">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <label for="featured_image" class="form-label fw-bold">Header Image</label>
                        <input class="form-control" type="file" id="featured_image" name="featured_image">
                        <small class="text-muted">Recommended size: 1200x800px and of type webp</small>
                        <div class="mt-2 preview-container d-none">
                            <img id="image-preview" class="img-fluid rounded" src="#" alt="Preview">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" id="remove-image">
                                Remove Image
                            </button>
                        </div>
                    </div>

                    <!-- Category Selection -->
                    <div class="col-md-6 col-sm-12 border-start border-5 border-info mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}" 
                                        {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> 
                </div>

                <!-- Newsletter Scheduling (New Addition) -->
                <div class="row py-3 border-top">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <label class="form-label fw-bold">Send Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="send_option" id="send-now" 
                                value="now" {{ old('send_option', 'now') == 'now' ? 'checked' : '' }}>
                            <label class="form-check-label" for="send-now">Send Immediately</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="send_option" id="send-later" 
                                value="scheduled" {{ old('send_option') == 'scheduled' ? 'checked' : '' }}>
                            <label class="form-check-label" for="send-later">Schedule for Later</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-3" id="schedule-datetime" style="display: none;">
                        <label for="scheduled_at" class="form-label fw-bold">Schedule Date & Time</label>
                        <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" 
                            value="{{ old('scheduled_at') }}">
                    </div>
                </div>

                <!-- Subscriber Targeting (New Addition) -->
                <div class="row py-3 border-top">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Send To</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="all-subscribers" 
                                value="all" {{ old('recipient_type', 'all') == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label" for="all-subscribers">All Subscribers</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="category-subscribers" 
                                value="category" {{ old('recipient_type') == 'category' ? 'checked' : '' }}>
                            <label class="form-check-label" for="category-subscribers">Subscribers of Selected Category Only</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="test-send" 
                                value="test" {{ old('recipient_type') == 'test' ? 'checked' : '' }}>
                            <label class="form-check-label" for="test-send">Test Send (Admin Only)</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <a href="{{route('dashboard')}}" class="btn text-danger">Cancel</a>
                    </div>
                    <div>
                        {{-- "Save As Draft" button --}}
                        <button type="submit" name="status" value="draft" class="btn secondary-btn me-2" id="save-draft-btn">Save As Draft</button>
                        
                        {{-- "Send Newsletter" button --}}
                        <button type="submit" name="status" value="published" class="btn btn-subscribe" id="publish-btn">
                            <span id="send-btn-text">Send Newsletter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    {{-- 1. Load TinyMCE library --}}
    <script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    {{-- 2. Define window.tinyMCEUploadConfig BEFORE TinyMCE_init.js --}}
    <script>
        window.tinyMCEUploadConfig = {
            uploadUrl: '{{ route("articles.upload-image") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    
    {{-- 3. Load your TinyMCE initialization script --}}
    <script src="{{ asset('assets/js/TinyMCE_init.js') }}"></script>
    
    {{-- Newsletter-specific JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sendNowRadio = document.getElementById('send-now');
            const sendLaterRadio = document.getElementById('send-later');
            const scheduleDatetime = document.getElementById('schedule-datetime');
            const sendBtnText = document.getElementById('send-btn-text');
            
            // Content method elements
            const methodEditor = document.getElementById('method-editor');
            const methodTemplate = document.getElementById('method-template');
            const editorContent = document.getElementById('editor-content');
            const templateContent = document.getElementById('template-content');
            const htmlTemplateInput = document.getElementById('html_template');
            const templatePreview = document.getElementById('template-preview');
            const templatePreviewContent = document.getElementById('template-preview-content');
            const templateEditOption = document.getElementById('template-edit-option');
            const removeTemplateBtn = document.getElementById('remove-template');
            
            function toggleScheduleOptions() {
                if (sendLaterRadio.checked) {
                    scheduleDatetime.style.display = 'block';
                    sendBtnText.textContent = 'Schedule Newsletter';
                } else {
                    scheduleDatetime.style.display = 'none';
                    sendBtnText.textContent = 'Send Newsletter';
                }
            }
            
            function toggleContentMethod() {
                if (methodTemplate.checked) {
                    editorContent.style.display = 'none';
                    templateContent.style.display = 'block';
                } else {
                    editorContent.style.display = 'block';
                    templateContent.style.display = 'none';
                    templatePreview.style.display = 'none';
                    templateEditOption.style.display = 'none';
                }
            }
            
            function handleTemplateUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) { // 2MB limit
                        alert('File size must be less than 2MB');
                        event.target.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const htmlContent = e.target.result;
                        
                        // Display preview (sanitized)
                        templatePreviewContent.innerHTML = htmlContent;
                        templatePreview.style.display = 'block';
                        templateEditOption.style.display = 'block';
                    };
                    reader.readAsText(file);
                }
            }
            
            function removeTemplate() {
                htmlTemplateInput.value = '';
                templatePreview.style.display = 'none';
                templateEditOption.style.display = 'none';
                templatePreviewContent.innerHTML = '';
            }
            
            // Event listeners
            sendNowRadio.addEventListener('change', toggleScheduleOptions);
            sendLaterRadio.addEventListener('change', toggleScheduleOptions);
            methodEditor.addEventListener('change', toggleContentMethod);
            methodTemplate.addEventListener('change', toggleContentMethod);
            htmlTemplateInput.addEventListener('change', handleTemplateUpload);
            removeTemplateBtn.addEventListener('click', removeTemplate);
            
            // Initialize on page load
            toggleScheduleOptions();
            toggleContentMethod();
        });
    </script>
@endpush