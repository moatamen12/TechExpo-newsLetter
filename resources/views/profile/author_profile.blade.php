@extends('layouts.app')
@section('title', 'Author Profile')
@section('content')

<div class="container-fluid py-4">
    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="row justify-content-center mb-3">
            <div class="col-md-10">
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

    {{-- Display Success Message --}}
    @if (session('success'))
        <div class="row justify-content-center mb-3">
            <div class="col-md-10">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        {{-- Profile Header Section --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 articall-card-hover">
                <div class="card-body text-center p-4">
                    {{-- Profile Avatar --}}
                    <div class="mb-3 d-flex justify-content-center">
                        <x-user_avatar :user="$user" size="100" />
                    </div>
                    
                    {{-- Profile Info --}}
                    <h3 class="mb-1" style="font-weight: 600;">{{ $user->name }}</h3>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    @if($user->userProfile && $user->userProfile->work)
                        <p class="text-muted mt-1" style="font-size: 0.9rem;">{{ $user->userProfile->work }}</p>
                    @endif
                    @if($user->userProfile && $user->userProfile->bio)
                        <p class="text-muted mt-2" style="font-size: 0.85rem;">{{ Str::limit($user->userProfile->bio, 80) }}</p>
                    @endif
                    
                    {{-- Social Links Display --}}
                    @if($user->userProfile && $user->userProfile->activeSocialLinks->count() > 0)
                        <div class="d-flex justify-content-center gap-2 mt-3 mb-3">
                            @foreach($user->userProfile->activeSocialLinks as $socialLink)
                                <a href="{{ $socialLink->url }}" target="_blank" class="btn btn-outline-secondary btn-sm" 
                                   style="color: {{ $socialLink->platform_color }}; border-color: {{ $socialLink->platform_color }};"
                                   title="{{ $socialLink->platform_name }}">
                                    <i class="{{ $socialLink->platform_icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    {{-- Stats Section --}}
                    <div class="d-flex justify-content-around pt-3 mt-3 border-top">
                        <div class="text-center px-2">
                            <h5 class="mb-0" style="font-weight: 700; color: var(--btn-color);">{{ $followersCount }}</h5>
                            <small class="text-muted" style="font-size: 0.75rem;">Followers</small> 
                        </div>
                        <div class="text-center px-2">
                            <h5 class="mb-0" style="font-weight: 700; color: var(--btn-color);">{{ $followingCount }}</h5>
                            <small class="text-muted" style="font-size: 0.75rem;">Following</small>
                        </div>
                        <div class="text-center px-2">
                            <h5 class="mb-0" style="font-weight: 700; color: var(--btn-color);">{{ $articlesCount }}</h5>
                            <small class="text-muted" style="font-size: 0.75rem;">Articles</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="col-md-9">
            {{-- Tab Navigation --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <ul class="nav nav-pills" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-profile-tab" 
                                    data-bs-toggle="pill" data-bs-target="#edit-profile" type="button" 
                                    role="tab" aria-controls="edit-profile" aria-selected="true">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="followers-tab" 
                                    data-bs-toggle="pill" data-bs-target="#followers" type="button" 
                                    role="tab" aria-controls="followers" aria-selected="false">
                                <i class="fas fa-users me-2"></i>Followers
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="following-tab" 
                                    data-bs-toggle="pill" data-bs-target="#following" type="button" 
                                    role="tab" aria-controls="following" aria-selected="false">
                                <i class="fas fa-user-plus me-2"></i>Following
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="saved-articles-tab" 
                                    data-bs-toggle="pill" data-bs-target="#saved-articles" type="button" 
                                    role="tab" aria-controls="saved-articles" aria-selected="false">
                                <i class="fas fa-bookmark me-2"></i>Saved Articles
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settings-tab" 
                                    data-bs-toggle="pill" data-bs-target="#settings" type="button" 
                                    role="tab" aria-controls="settings" aria-selected="false">
                                <i class="fas fa-cog me-2"></i>Settings
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Tab Content --}}
                <div class="card-body">
                    <div class="tab-content" id="profileTabContent">
                        {{-- Edit Profile Tab --}}
                        <div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                            <h4 class="mb-3">Edit Profile</h4>
                            
                            <form action="{{ route('author.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                {{-- Profile Photo Section --}}
                                <div class="mb-4">
                                    <h5>Profile Photo</h5>
                                    <div class="d-flex align-items-center p-3" style="background-color: var(--secoundary-bg); border-radius: 8px;">
                                        <div class="me-3">
                                            <x-user_avatar :user="$user" size="80" />
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
                                    <h5>Basic Information</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" required>
                                            <x-error_msg field="name"/>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                            <x-error_msg field="email"/>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="work" class="form-label">Work/Position</label>
                                            <input type="text" id="work" name="work" class="form-control" value="{{ $user->userProfile->work ?? '' }}" placeholder="e.g., Software Engineer at Company">
                                            <x-error_msg field="work"/>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" id="website" name="website" class="form-control" value="{{ $user->userProfile->website ?? '' }}" placeholder="https://yourwebsite.com">
                                            <x-error_msg field="website"/>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bio Section --}}
                                <div class="mb-4">
                                    <h5>About You</h5>
                                    <div class="form-group">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea id="bio" name="bio" rows="4" class="form-control" placeholder="Tell us about yourself...">{{ $user->userProfile->bio ?? '' }}</textarea>
                                        <x-error_msg field="bio"/>
                                    </div>
                                </div>

                                {{-- Social Links Section --}}
                                <div class="mb-4">
                                    <h5>Social Links</h5>
                                    <div id="social-links-container">
                                        @php
                                            $userSocialLinks = $user->userProfile ? $user->userProfile->socialLinks->keyBy('platform') : collect();
                                        @endphp
                                        
                                        @foreach(\App\Models\SocialLink::$platforms as $platform => $platformName)
                                            @php
                                                $existingLink = $userSocialLinks->get($platform);
                                            @endphp
                                            <div class="row g-3 mb-3 social-link-row">
                                                <div class="col-md-3">
                                                    <label class="form-label">
                                                        <i class="{{ \App\Models\SocialLink::$platformIcons[$platform] }} me-2" 
                                                           style="color: {{ \App\Models\SocialLink::$platformColors[$platform] }};"></i>
                                                        {{ $platformName }}
                                                    </label>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="url" 
                                                           name="social_links[{{ $platform }}]" 
                                                           class="form-control" 
                                                           value="{{ $existingLink ? $existingLink->url : '' }}" 
                                                           placeholder="Enter your {{ $platformName }} URL">
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="social_active[{{ $platform }}]"
                                                               {{ $existingLink && $existingLink->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">Add your social media profiles. Only active links will be displayed on your profile.</small>
                                </div>

                                {{-- Password Section --}}
                                <div class="mb-4">
                                    <h5>Security</h5>
                                    <button type="button" class="btn secondary-btn btn-sm" data-bs-toggle="collapse" data-bs-target="#passwordChangeSection" aria-expanded="false">
                                        <i class="fas fa-lock me-2"></i>Change Password
                                    </button>
                                    
                                    <div class="collapse mt-3" id="passwordChangeSection">
                                        <div class="p-3" style="background-color: var(--secoundary-bg); border-radius: 8px;">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <small class="text-muted">Please enter your current password to set a new one.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="old_password" class="form-label">Current Password</label>
                                                    <div class="input-group">
                                                        <input type="password" id="old_password" name="old_password" class="form-control" autocomplete="current-password">
                                                        <x-visibility-toggle target="old_password"/>
                                                    </div>
                                                    <x-error_msg field="old_password"/>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="new_password" class="form-label">New Password</label>
                                                    <div class="input-group">
                                                        <input type="password" id="new_password" name="new_password" class="form-control" autocomplete="new-password">
                                                        <x-visibility-toggle target="new_password"/>
                                                    </div>
                                                    <small class="text-muted">Must be at least 8 characters</small>
                                                    <x-error_msg field="new_password"/>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                                    <div class="input-group">
                                                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" autocomplete="new-password">
                                                        <x-visibility-toggle target="new_password_confirmation"/>
                                                    </div>
                                                    <x-error_msg field="new_password_confirmation"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Form Actions --}}
                                <div class="text-end">
                                    <button type="submit" class="btn btn-subscribe">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Followers Tab --}}
                        <div class="tab-pane fade" id="followers" role="tabpanel" aria-labelledby="followers-tab">
                            <h4 class="mb-3">Your Followers</h4>
                            
                            @if($followers && $followers->count() > 0)
                                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                                    @foreach($followers as $followerRelation)
                                        @php
                                            $follower = $followerRelation->follower;
                                        @endphp
                                        @if($follower)
                                            <div class="col">
                                                <div class="card h-100 text-center articall-card-hover" style="min-height: 120px;">
                                                    <div class="card-body p-2">
                                                        <div class="mb-2">
                                                            <x-user_avatar :user="$follower" size="40" />
                                                        </div>
                                                        <h6 class="card-title mb-1 over_hid" style="font-size: 0.8rem; font-weight: 600;">
                                                            {{ $follower->name ?? 'User' }}
                                                        </h6>
                                                        <small class="text-muted over_hid" style="font-size: 0.7rem;">{{ $follower->email }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No followers yet</h5>
                                    <p class="text-muted">Start creating great content to attract followers!</p>
                                </div>
                            @endif
                        </div>

                        {{-- Following Tab --}}
                        <div class="tab-pane fade" id="following" role="tabpanel" aria-labelledby="following-tab">
                            <h4 class="mb-3">Following</h4>
                            
                            @if($following && $following->count() > 0)
                                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                                    @foreach($following as $followingRelation)
                                        @php
                                            $followedUser = $followingRelation->following->user ?? null;
                                        @endphp
                                        @if($followedUser)
                                            <div class="col">
                                                <div class="card h-100 text-center articall-card-hover" style="min-height: 140px;">
                                                    <div class="card-body p-2">
                                                        <div class="mb-2">
                                                            <x-user_avatar :user="$followedUser" size="40" />
                                                        </div>
                                                        <h6 class="card-title mb-1 over_hid" style="font-size: 0.8rem; font-weight: 600;">
                                                            {{ $followedUser->name ?? 'User' }}
                                                        </h6>
                                                        <small class="text-muted over_hid d-block mb-2" style="font-size: 0.7rem;">{{ $followedUser->email }}</small>
                                                        <a href="{{ route('profile.show', $followingRelation->following->profile_id) }}" 
                                                           class="btn btn-outline-primary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                                            View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                                    <h5>Not following anyone yet</h5>
                                    <p class="text-muted">Discover and follow authors whose content you enjoy!</p>
                                </div>
                            @endif
                        </div>

                        {{-- Saved Articles Tab --}}
                        <div class="tab-pane fade" id="saved-articles" role="tabpanel" aria-labelledby="saved-articles-tab">
                            <h4 class="mb-3">Saved Articles</h4>
                            
                            @if($savedArticles && $savedArticles->count() > 0)
                                <div class="list-group">
                                    @foreach($savedArticles as $savedArticle)
                                        @if($savedArticle->article)
                                            <div class="list-group-item list-group-item-action articall-card-hover">
                                                <div class="d-flex w-100 justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <a href="{{ route('articles.show', $savedArticle->article->article_id) }}" 
                                                               class="stretched-link text-decoration-none">
                                                                {{ $savedArticle->article->title ?? 'Untitled Article' }}
                                                            </a>
                                                        </h6>
                                                        <p class="mb-1 text-muted">
                                                            By {{ $savedArticle->article->author->user->name ?? 'Unknown Author' }}
                                                            • Saved {{ $savedArticle->saved_at ? $savedArticle->saved_at->format('M j, Y') : 'N/A' }}
                                                        </p>
                                                        <small class="text-muted">
                                                            {{ $savedArticle->article->categorie->name ?? 'Uncategorized' }}
                                                        </small>
                                                    </div>
                                                    <small class="text-muted ms-3">
                                                        <x-reading_time :article="$savedArticle->article"/>
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                                    <h5>No saved articles</h5>
                                    <p class="text-muted">Start saving articles you want to read later!</p>
                                </div>
                            @endif
                        </div>

                        {{-- Settings Tab --}}
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            <h4 class="mb-3">Account Settings</h4>
                            
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>Delete Account</h6>
                                            <p class="text-muted">
                                                Permanently delete your account and all associated data. This action cannot be undone.
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <form action="{{ route('author.profile.delete') }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action is irreversible.');">
                                                @csrf
                                                @method('DELETE')
                                                <div class="mb-3">
                                                    <label for="password_delete" class="form-label">Verify Password</label>
                                                    <div class="input-group">
                                                        <input type="password" id="password_delete" name="password_delete" 
                                                               class="form-control form-control-sm" required autocomplete="current-password" 
                                                               placeholder="Enter password">
                                                        <x-visibility-toggle target="password_delete"/>
                                                    </div>
                                                    @error('password_delete', 'deleteAccount')
                                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                                    @enderror
                                                    @if(session('error_delete_account'))
                                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ session('error_delete_account') }}</div>
                                                    @endif
                                                </div>
                                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                                    <i class="fas fa-trash me-2"></i>Delete Account
                                                </button>
                                            </form>
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
</div>

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
            var collapseElement = new bootstrap.Collapse(passwordChangeSection, {
                toggle: false
            });
            collapseElement.show();
        }

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
            });

            if (passwordChangeSection.classList.contains('show')) {
                oldPasswordInput.setAttribute('required', 'required');
                newPasswordInput.setAttribute('required', 'required');
                newPasswordConfirmationInput.setAttribute('required', 'required');
            }
        }

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
    });
</script>
@endpush

@endsection