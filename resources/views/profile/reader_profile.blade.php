@extends('layouts.app')
@section('title', 'Profile')
@section('content')
@php 
    // dd('$savedArticles'); 
@endphp
<section class="container-fluid p-3">
        {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="row justify-content-center">
            <div class="col-md-9"> {{-- Match the width of your main content area --}}
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Please fix the following errors:</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
    {{-- Display Success Message --}}
    @if (session('success'))
        <div class="row justify-content-center">
            <div class="col-md-9"> {{-- Match the width of your main content area --}}
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Profile Header Section (Left Column) -->
        <div class="col-md-3"> {{-- Changed from col-md-4 to col-md-3 --}}
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    {{-- Avatar section removed --}}
                                    
                                    <!-- Profile Info & Stats -->
                                    <div class="col">
                                        <div class="d-flex flex-column"> {{-- Stacks User Info above Stats Block --}}
                                            {{-- User Name and Email --}}
                                            <div class="text-center mb-3"> {{-- Centered user info and added bottom margin --}}
                                                <h2 class="mb-1" style="font-weight: 600;">HI! {{$user_name}}</h2> {{-- Adjusted margin --}}
                                                <p class="text-muted mb-2" style="font-size: 0.9rem;">{{$user_email}}</p> {{-- Adjusted margin --}}
                                                {{-- Add Become a Writer button  {{ route('writer.registration.form') }}--}}
                                                <a href="#" class="btn btn-subscribe btn-sm m-3">Become a Writer</a> 
                                                <p class="text-muted mt-1" style="font-size: 0.85rem;">Feel Free To edit Your Profile</p> {{-- Changed to <p>, adjusted style --}}
                                            </div>
                                            
                                            {{-- Stats Section --}}
                                            <div class="d-flex justify-content-around pt-3 mt-3 border-top"> {{-- Added padding-top, margin-top, and border-top --}}
                                                {{-- Stat Item 1: Articles Read --}}
                                                <div class="text-center px-2"> {{-- Added horizontal padding --}}
                                                    <h5 class="mb-0" style="font-weight: 700;">{{ $followedWriters->count() }}</h5>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Following</small> 
                                                </div>
                                                {{-- Stat Item 2: Writers Following --}}
                                                <div class="text-center px-2"> {{-- Added horizontal padding --}}
                                                    <h5 class="mb-0" style="font-weight: 700;">{{ $savedArticles->total() }}</h5>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Saved Article</small>
                                                </div>
                                                {{-- Stat Item 3: Reactions
                                                <div class="text-center px-2"> Added horizontal padding
                                                    <h5 class="mb-0" style="font-weight: 700;">12</h5>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Reactions</small>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column for Tabs and Form --> 
        <div class="col-md-9">
            <!-- Navigation Tabs -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12 ">
                        <ul class="nav nav-pills border-bottom shadow-sm border-0 rounded" id="profileTab" role="tablist" style="border-color: #e9ecef !important; background-color: #ffffff !important;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active border-0 bg-transparent m-2" id="edit-profile-tab" 
                                        data-bs-toggle="pill" data-bs-target="#edit-profile" type="button" 
                                        role="tab" aria-controls="edit-profile" aria-selected="true"
                                        style="color: var(--primary-text); font-weight: 500;">
                                    Edit Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link border-0 bg-transparent m-2" id="Following-tab" 
                                        data-bs-toggle="pill" data-bs-target="#Following" type="button" 
                                        role="tab" aria-controls="Following" aria-selected="false"
                                        style="color: var(--tab-txt); font-weight: 500;">
                                    Following
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link border-0 bg-transparent m-2" id="Saved-tab" 
                                        data-bs-toggle="pill" data-bs-target="#Saved" type="button" 
                                        role="tab" aria-controls="Saved" aria-selected="false"
                                        style="color: var(--tab-txt); font-weight: 500;">
                                    Saved Articles
                                </button>
                            </li>
                            {{-- New Settings Tab --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link border-0 bg-transparent m-2" id="settings-tab" 
                                        data-bs-toggle="pill" data-bs-target="#settings" type="button" 
                                        role="tab" aria-controls="settings" aria-selected="false"
                                        style="color: var(--tab-txt); font-weight: 500;">
                                    Settings
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content card m-2 boreder-1 border-light-subtle shadow-sm" id="profileTabContent">
                {{-- Edit Profile Tab Pane (Contains the Form) --}}
                <div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                    <h3 class="p-3">Edit Profile</h3>
                    <div class="container mt-4 mb-4"> {{-- Removed d-flex, align-items-center, justify-content-center --}}
                        <form action="{{ route('profile.update') }}" method="POST" class="w-100">
                            @csrf
                            <div class="row g-3">
                                {{-- name --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label mt-2">Your Full Name</label>
                                    <input type="text" class="form-control form-control-sm" name="name"
                                            id="name" placeholder="Enter Your New Full Name" aria-required="true" 
                                            value="{{ $user_name }}" >
                                    <x-error_msg field="name"/>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label for="email" class="form-label mt-2">Your Email</label>
                                    <input type="email" class="form-control form-control-sm" name="email"
                                            id="email" placeholder="Enter Your New Email" aria-required="true" 
                                            value="{{ $user_email }}" >
                                    <x-error_msg field="email"/>
                                </div>

                                {{-- Button to toggle password change fields using Bootstrap Collapse --}}
                                <div class="col-12 mt-4">
                                    <button type="button" class="btn secondary-btn btn-sm" data-bs-toggle="collapse" data-bs-target="#passwordChangeSection" aria-expanded="false" aria-controls="passwordChangeSection">
                                        Change Password
                                    </button>
                                </div>

                                {{-- Collapsible password change section --}}
                                <div class="collapse row g-3 mt-2" id="passwordChangeSection">
                                    <span class="text-muted col-12">You Have Enter your current password to set a new one.</span>
                                    {{-- Old Password --}}
                                    <div class="col-md-6">
                                        <label for="old_password" class="form-label mt-2">Old Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-sm" id="old_password"
                                                   placeholder="Enter your Old Password" name="old_password"
                                                   autocomplete="current-password">
                                            <x-visibility-toggle target="old_password"/>
                                        </div>
                                        <x-error_msg field="old_password"/>
                                    </div>
                                    <div class="col-md-6"></div> {{-- Spacer column --}}

                                    {{-- New Password --}}
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label mt-2">New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-sm" id="new_password" 
                                                    placeholder="Enter your New Password" name="new_password"
                                                    autocomplete="new-password">
                                            <x-visibility-toggle target="new_password"/>
                                        </div>
                                        <footer class="text-muted">Must be at least 8 characters have at least 1 letter and 1 cpechila latter</footer>
                                        <x-error_msg field="new_password"/>
                                    </div>
                            
                                    <!-- Confirm New password -->
                                    <div class="col-md-6">
                                        <label for="new_password_confirmation" class="form-label mt-2">Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-sm" id="new_password_confirmation" name="new_password_confirmation" 
                                                    placeholder="Confirm your New Password" autocomplete="new-password">
                                            <x-visibility-toggle target="new_password_confirmation"/>
                                        </div>
                                        <x-error_msg field="new_password_confirmation"/>
                                    </div>
                                </div>
                                {{-- End of collapsible password change section --}}

                                <div class="d-flex align-items-center justify-content-end col-12"> {{-- Ensure button is full width of this row --}}
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-subscribe btn-sm w-100">Update Profile</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Writers You Follow Tab Pane --}}
                <div class="tab-pane fade" id="Following" role="tabpanel" aria-labelledby="Following-tab">
                    <div class="container py-4">
                        <h3 class="mb-4" style="font-weight: 600; color: #333;">Writers You Follow</h3>
                        
                        @if($followedWriters && $followedWriters->count() > 0)
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                                @foreach($followedWriters as $followedRelation)
                                    @php
                                        $writer = $followedRelation->following;
                                    @endphp
                                    @if($writer && $writer->user)
                                    <div class="col">
                                        <div class="card h-100 text-center shadow-sm">
                                            <div class="card-body d-flex flex-column align-items-center">
                                                <div class="mb-3">
                                                    <x-user_avatar :user="$writer->user" size="80" /> 
                                                </div>
                                                <h5 class="card-title mb-1">
                                                    <a href="{{route('profile.show',$writer->profile_id)}}" class="text-decoration-none stretched-link">{{ $writer->user->name ?? 'Writer Name Unavailable' }}</a>
                                                </h5>
                                                <div class="mt-auto"> {{-- Pushes button to the bottom if card heights vary --}}
                                                    {{-- Replace # with the actual unfollow route and $writer->user->user_id or $writer->profile_id as needed --}}
                                                    {{-- Removed old form --}}
                                                    <button type="button" class="btn secondary-btn btn-sm px-3 js-follow-button" 
                                                            style="border-radius: 20px; font-weight: 500; position: relative; z-index: 2;"
                                                            data-profile-id="{{ $writer->profile_id }}"
                                                            data-is-following="true"
                                                            data-unfollow-url="{{ route('interactions.profiles.unfollow', $writer->profile_id) }}"
                                                            data-follow-url="{{ route('interactions.profiles.follow', $writer->profile_id) }}">
                                                        Unfollow
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-center">You are not following any writers yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Saved Tab Pane --}}
                <div class="tab-pane fade" id="Saved" role="tabpanel" aria-labelledby="Saved-tab">
                    <div class="container py-4">
                        <h3 class="mb-4" style="font-weight: 600; color: #333;">Saved Articles</h3>
                        @if($savedArticles && $savedArticles->count() > 0)
                            @foreach($savedArticles as $savedItem) {{-- Renamed loop variable for clarity --}}
                                @if($savedItem->article) {{-- Check if the related article exists --}}
                                    <div class="position-relative">
                                        <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                            <div class="flex-grow-1">
                                                <a href="{{ route('articles.show', $savedItem->article->article_id) }}" class="stretched-link fw-bold display-7 me-2 text-decoration-none">
                                                    {{ $savedItem->article->title ?? 'Untitled Article' }}
                                                </a><br>
                                                <small class="text-muted">
                                                    By {{ $savedItem->article->author->user->name ?? 'Unknown Author' }}
                                                    • Saved on {{ $savedItem->saved_at ? $savedItem->saved_at->format('M j, Y') : 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Move button outside the stretched-link container -->
                                        <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 text-end">
                                                    <span class="d-block text-muted" style="font-size: 14px;">
                                                        <x-reading_time :article="$savedItem->article"/>
                                                    </span>
                                                    <span class="d-block text-muted" style="font-size: 12px;">
                                                        {{ $savedItem->article->categorie->name ?? 'Uncategorized' }}
                                                    </span>
                                                </div>
                                                {{-- Replace form with AJAX button --}}
                                                <button type="button" class="p-2 border-0 bg-transparent js-save-article-button"
                                                        data-saved="true" {{-- Article is currently saved --}}
                                                        data-save-url="{{ route('articles.save', $savedItem->article->article_id) }}"
                                                        data-unsave-url="{{ route('articles.unsave', $savedItem->article->article_id) }}"
                                                        title="Unsave article">
                                                    <i class="save-icon fa-solid fa-bookmark" style="color: #0d6efd;"></i> {{-- Icon for "saved" state --}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p>No saved articles yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Settings Tab Pane --}}
                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <div class="container py-4">
                        <h3 class="mb-4" style="font-weight: 600; color: #333;">Account Settings</h3>
                        
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                Delete Account
                            </div>
                            <div class="card-body">
                                <p class="card-text text-danger">
                                    <strong>Warning:</strong> Deleting your account is permanent and cannot be undone. 
                                    All your data, including saved articles and followed writers, will be permanently removed.
                                </p>
                                {{-- {{ route('profile.delete') }} --}}
                                <form action="{{ route('profile.delete') }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action is irreversible.');">
                                    @csrf
                                    @method('DELETE')
                                    <div class="mb-3">
                                        <label for="password_delete" class="form-label">Verify Password to Delete Account <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-sm @error('password_delete', 'deleteAccount') is-invalid @enderror" 
                                                   id="password_delete" name="password_delete" 
                                                   placeholder="Enter your current password" required autocomplete="current-password">
                                            <x-visibility-toggle target="password_delete"/>
                                        </div>
                                        @error('password_delete', 'deleteAccount')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        {{-- General error from controller if password check fails but not a validation rule --}}
                                        @if(session('error_delete_account'))
                                            <div class="text-danger mt-2">
                                                {{ session('error_delete_account') }}
                                            </div>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm">Delete My Account Permanently</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- End of Settings Tab Pane --}}

            </div> 
        </div>
    </div>
</section>
<script src="{{ asset('assets/js/visability.js') }}"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordChangeSection = document.getElementById('passwordChangeSection');
        const oldPasswordInput = document.getElementById('old_password');
        const newPasswordInput = document.getElementById('new_password');
        const newPasswordConfirmationInput = document.getElementById('new_password_confirmation');

        // If there are password validation errors on page load, show the password section
        let hasPasswordError = false;
        @if($errors->has('old_password') || $errors->has('new_password') || $errors->has('new_password_confirmation'))
            hasPasswordError = true;
        @endif

        if (hasPasswordError && passwordChangeSection) {
            // Use Bootstrap's API to show the collapse element
            var collapseElement = new bootstrap.Collapse(passwordChangeSection, {
                toggle: false // Prevent toggling, just ensure it's shown
            });
            collapseElement.show();
        }

        // Optional: Manage 'required' attribute based on collapse state
        // This is useful if your backend validation for password fields is conditional
        if (passwordChangeSection) {
            passwordChangeSection.addEventListener('show.bs.collapse', function () {
                oldPasswordInput.setAttribute('required', 'required');
                newPasswordInput.setAttribute('required', 'required');
                newPasswordConfirmationInput.setAttribute('required', 'required');
            });

            passwordChangeSection.addEventListener('hide.bs.collapse', function () {
                oldPasswordInput.removeAttribute('required');
                newPasswordInput.removeAttribute('required');
                newPasswordConfirmationInput.removeAttribute('required');
                // Optionally clear the fields when hiding
                // oldPasswordInput.value = '';
                // newPasswordInput.value = '';
                // newPasswordConfirmationInput.value = '';
            });

            // Initial check for required attributes if the section is already visible (e.g. due to error)
            if (passwordChangeSection.classList.contains('show')) {
                oldPasswordInput.setAttribute('required', 'required');
                newPasswordInput.setAttribute('required', 'required');
                newPasswordConfirmationInput.setAttribute('required', 'required');
            }
        }
    });
</script>
@endpush
@endsection
