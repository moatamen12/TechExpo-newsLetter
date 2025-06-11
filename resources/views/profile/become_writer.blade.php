@extends('layouts.app')
@section('title', 'Become a Writer')
@push('styles')
<style>
    i{ color: var(--btn-hover) !important; }
</style>
@endpush
@section('content')

<div class="container py-5">
    {{-- Hero Section --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h1 class="display-5 fw-bold mb-3" style="color: var(--btn-hover);">
                <i class="fas fa-pen-fancy me-3"></i>Become a Writer
            </h1>
            <p class="lead text-muted mb-4">
                Share your thoughts, stories, and expertise with our community. Start your writing journey today!
            </p>
        </div>
    </div>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Please fix the following errors:</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Form --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white border-0 py-4">
                    <div class="text-center">
                        <h3 class="mb-2">Set Up Your Writer Profile</h3>
                        <p class="text-muted">Fill in your details below or skip to start writing immediately</p>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('writer.registration.submit') }}" method="POST" enctype="multipart/form-data" id="writerForm">
                        @csrf
                        
                        {{-- Profile Photo Section --}}
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-camera me-2"></i>Profile Photo
                                <small class="text-muted">(Optional)</small>
                            </h5>
                            <div class="d-flex align-items-center p-3" style="background-color: var(--secoundary-bg); border-radius: 8px;">
                                <div class="me-3">
                                    <div class="current-photo" style="width: 80px; height: 80px; border-radius: 50%; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user fa-2x text-muted"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="form-control form-control-sm">
                                    <small class="text-muted">Upload a profile photo (JPG, PNG, max 2MB)</small>
                                    <x-error_msg field="profile_photo"/>
                                </div>
                            </div>
                        </div>

                        {{-- Basic Information --}}
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>About You
                                <small class="text-muted">(Optional)</small>
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="work" class="form-label">Work/Position</label>
                                    <input type="text" id="work" name="work" class="form-control" value="{{ old('work') }}" placeholder="e.g., Software Engineer at Company">
                                    <x-error_msg field="work"/>
                                </div>
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" id="website" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://yourwebsite.com">
                                    <x-error_msg field="website"/>
                                </div>
                            </div>
                        </div>

                        {{-- Bio Section --}}
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-align-left me-2"></i>Bio
                                <small class="text-muted">(Optional)</small>
                            </h5>
                            <div class="form-group">
                                <label for="bio" class="form-label">Tell us about yourself</label>
                                <textarea id="bio" name="bio" rows="4" class="form-control" placeholder="Share your story, interests, and what you love to write about...">{{ old('bio') }}</textarea>
                                <small class="text-muted">Maximum 1000 characters</small>
                                <x-error_msg field="bio"/>
                            </div>
                        </div>

                        {{-- Social Links Section --}}
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-share-alt me-2"></i>Social Links
                                <small class="text-muted">(Optional)</small>
                            </h5>
                            <div id="social-links-container">
                                @php
                                    $platforms = [
                                        'twitter' => 'Twitter',
                                        'linkedin' => 'LinkedIn', 
                                        'github' => 'GitHub',
                                        'website' => 'Personal Website',
                                        'instagram' => 'Instagram',
                                        'facebook' => 'Facebook'
                                    ];
                                @endphp
                                
                                @foreach($platforms as $platform => $platformName)
                                    <div class="row g-3 mb-3 social-link-row">
                                        <div class="col-md-3">
                                            <label class="form-label">
                                                <i class="fab fa-{{ $platform }} me-2"></i>{{ $platformName }}
                                            </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="url" 
                                                   name="social_links[{{ $platform }}]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="https://{{ $platform }}.com/yourprofile"
                                                   value="{{ old('social_links.'.$platform) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="social_active[{{ $platform }}]" 
                                                       class="form-check-input" 
                                                       id="active_{{ $platform }}"
                                                       {{ old('social_active.'.$platform) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="active_{{ $platform }}">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Only active links will be displayed on your profile.</small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <button type="submit" name="skip_details" value="1" class="btn secondary-btn btn-lg">
                                <i class="fas fa-forward me-2"></i>Skip for Now
                            </button>
                            <button type="submit" class="btn btn-subscribe btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Writer Profile
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                You can always update your profile details later from your dashboard
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/visability.js') }}"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // File upload preview
        const fileInput = document.getElementById('profile_photo');
        const currentPhoto = document.querySelector('.current-photo');
        
        if (fileInput && currentPhoto) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        currentPhoto.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Character counter for bio
        const bioTextarea = document.getElementById('bio');
        if (bioTextarea) {
            const maxLength = 1000;
            const counter = document.createElement('small');
            counter.className = 'text-muted float-end';
            bioTextarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - bioTextarea.value.length;
                counter.textContent = `${remaining} characters remaining`;
                counter.className = remaining < 100 ? 'text-warning float-end' : 'text-muted float-end';
            }
            
            bioTextarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
</script>
@endpush

@endsection