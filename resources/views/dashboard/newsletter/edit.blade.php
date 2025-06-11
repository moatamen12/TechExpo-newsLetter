@extends('layouts.app')
@section('title', 'Edit Newsletter')

@php
    $btn = [
        'link' => route('newsletter.show', $newsletter->id),
        'text' => '<i class="fas fa-arrow-left me-2"></i>Back to Preview'
    ];
@endphp

@section('content')
<div class="container px-5">
    <x-dashboard-header 
        title="Edit Newsletter" 
        description="Update your newsletter content and settings"
        :btn="[$btn]"
        class="mb-2">
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

    <form action="{{ route('newsletter.update', $newsletter->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-4 border-0 bg-white shadow-sm rounded-3">
            <div class="p-3 bg-white rounded-3">
                <!-- Newsletter Subject -->
                <div class="mb-4">
                    <label for="title" class="form-label fw-bold">Newsletter Subject</label>
                    <input type="text" class="form-control form-control-sm" id="title" name="title" 
                        placeholder="Enter your newsletter subject line" minlength="10" maxlength="100"
                        value="{{ old('title', $newsletter->title) }}" required>
                    <small class="text-muted">Subject should be 10-100 characters</small>
                </div>

                <!-- Newsletter Preview Text -->
                <div class="mb-4">
                    <label for="summary" class="form-label fw-bold">Preview Text</label>
                    <input type="text" class="form-control form-control-sm" id="summary" name="summary" 
                        placeholder="This text appears in email previews" 
                        minlength="30" maxlength="300" value="{{ old('summary', $newsletter->summary) }}" required>
                    <small class="text-muted">Preview text should be 30-300 characters</small>
                </div>
            </div>
        </div>

        <!-- Newsletter Content -->
        <div class="mb-4">
            <label for="content" class="form-label fw-bold">Newsletter Content</label>
            <textarea id="content" name="content">{{ old('content', $newsletter->content) }}</textarea>
        </div>

        <div class="mb-4 border border-0 rounded-3 p-3 bg-white shadow-sm">
            <div class="row py-3">
                <!-- Featured Image -->
                <div class="col-md-6 col-sm-12 mb-3">
                    <label for="featured_image" class="form-label fw-bold">Header Image</label>
                    
                    @if($newsletter->featured_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $newsletter->featured_image) }}" 
                                 class="img-fluid rounded" alt="Current featured image" style="max-height: 200px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" 
                                       id="remove_featured_image" name="remove_featured_image">
                                <label class="form-check-label" for="remove_featured_image">
                                    Remove current image
                                </label>
                            </div>
                        </div>
                    @endif
                    
                    <input class="form-control" type="file" id="featured_image" name="featured_image">
                    <small class="text-muted">Recommended size: 1200x800px and of type webp</small>
                </div>

                <!-- Category Selection -->
                <div class="col-md-6 col-sm-12 border-start border-5 border-info mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" 
                                    {{ old('category_id', $newsletter->category_id) == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('newsletter.show', $newsletter->id) }}" class="btn text-danger">Cancel</a>
                </div>
                <div>
                    <button type="submit" class="btn btn-subscribe">
                        <i class="fas fa-save me-2"></i>Update Newsletter
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
        window.tinyMCEUploadConfig = {
            uploadUrl: '{{ route("articles.upload-image") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    
    <script src="{{ asset('assets/js/TinyMCE_init.js') }}"></script>
@endpush