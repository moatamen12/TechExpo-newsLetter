@extends('layouts.app')
@section('title', 'Create Article')
@php
    $btn =  [
        'link' => route('dashboard.articles'),
        'text' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" style="margin-right: 5px;">
            <path fill="white" d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288 480 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-370.7 0 73.4-73.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-128 128z"/></svg>'
    ];

@endphp
@section('content')
<div class="container px-5 ">
    <x-dashboard-header 
        title="Create New Article/Newsletter" 
        description="Write and publish a new article/newsletter"
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
                    <!-- Article Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">newsletter.Article Title</label>
                        <input type="text" class=" form-control form-control-sm" id="title" name="title" 
                            placeholder="Enter Your newsletter/Artilce Title " minlength="10" maxlength="100"
                            value="{{ old('title') }}" required>
                            <small class="text-muted">Title should be 10-100 characters</small>
                    </div>
                    <!-- Article Summary -->
                    <div class="mb-4">
                        <label for="summary" class="form-label fw-bold">newsletter/Article Summary</label>
                        <input type="text" class="form-control form-control-sm" id="summary" name="summary" 
                            placeholder="Summary is a preview text that help readers shous your Article/newsletter" 
                            minlength="30" maxlength="300" value="{{ old('summary') }}" required>
                            <small class="text-muted">Summary should be 30-300 characters</small>
                    </div>
                </div>
            </div>

            <div class="">
                <!-- Article Content -->
                <div class="mb-4">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea id="content" name="content">{{ old('content') }}</textarea>
                </div>
            </div>

            <div class="mb-4 border border-0 rounded-3 p-3 bg-white shadow-sm">
                {{-- featuerd image --}}
                <div class="row py-3">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <label for="featured_image" class="form-label fw-bold">Featured Image</label>
                        <input class="form-control" type="file" id="featured_image" name="featured_image">
                        <small class="text-muted">Recommended size: 1200x800px and of type webp</small>
                        <div class="mt-2 preview-container d-none">
                            <img id="image-preview" class="img-fluid rounded" src="#" alt="Preview">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" id="remove-image">
                                Remove Image
                            </button>
                        </div>
                    </div>

                    {{-- <!-- Status -->
                    <div class="col-md-3 col-sm-12 mb-3 border-start border-5 border-info">
                        <label class="form-label fw-bold">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status-draft" 
                                value="draft" {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status-draft">
                                Save as Draft
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status-published" 
                                value="published" {{ old('status') == 'published' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status-published">
                                Publish Now
                            </label>
                        </div>
                    </div> --}}

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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <a href="{{route('dashboard')}}" class="btn text-danger">Cancel</a>
                    </div>
                    <div>
                        {{-- "Save As Draft" button --}}
                        <button type="submit" name="status" value="draft" class="btn secondary-btn me-2" id="save-draft-btn">Save As Draft</button>
                        
                        {{-- "Post Article" button --}}
                        <button type="submit" name="status" value="published" class="btn btn-subscribe" id="publish-btn">Publish Article</button>
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
    
    {{-- Any other page-specific JavaScript can go here --}}
@endpush