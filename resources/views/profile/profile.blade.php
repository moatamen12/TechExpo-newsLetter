@extends('layouts.app')
@section('title', 'Profile')
@php
    $btn = [
        'link' => route('home'),
        'text' => 'Become a Writer
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" 
                    width="20" height="20" class="me-2 align-text-bottom" 
                    style="fill: currentColor;">
                    <path d="M278.5 215.6L23 471c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l57-57 68 0c49.7 0 97.9-14.4 139-41c11.1-7.2 5.5-23-7.8-23c-5.1 0-9.2-4.1-9.2-9.2c0-4.1 2.7-7.6 6.5-8.8l81-24.3c2.5-.8 4.8-2.1 6.7-4l22.4-22.4c10.1-10.1 2.9-27.3-11.3-27.3l-32.2 0c-5.1 0-9.2-4.1-9.2-9.2c0-4.1 2.7-7.6 6.5-8.8l112-33.6c4-1.2 7.4-3.9 9.3-7.7C506.4 207.6 512 184.1 512 160c0-41-16.3-80.3-45.3-109.3l-5.5-5.5C432.3 16.3 393 0 352 0s-80.3 16.3-109.3 45.3L139 149C91 197 64 262.1 64 330l0 55.3L253.6 195.8c6.2-6.2 16.4-6.2 22.6 0c5.4 5.4 6.1 13.6 2.2 19.8z"/></svg>',
        'class' => 'btn btn-lg secondary-btn mx-1'  
                ];
    $profileTabs = [
        [
            'id' => 'Reactions-tab',
            'ariaControls' => 'ReactionsContent',
            'txt' => 'info'
        ],
        [
            'id' => 'Saved-tab',
            'ariaControls' => 'SavedContent',
            'txt' => 'Saved'
        ],
        [
            'id' => 'Following-tab',
            'ariaControls' => 'FollowingContent',
            'txt' => 'Following'
        ]
    ];
    // $activeTab = 'info';
@endphp
@section('content')
    <div class="container my-4 p-4">
            <!-- User Profile Card -->
            <div class="card mb-4 shadow-sm p-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <!-- Left: Profile Avatar -->
                        <div class="me-4">
                            <x-profile_avatar :user="$user" :size="80" />
                        </div>
                        
                        <!-- Middle: User Information -->
                        <div class="flex-grow-1">
                            <h3 class="fw-bold mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            
                            <!-- Stats Row -->
                            <div class="d-flex mt-2">
                                <div class="me-4">
                                    <span class="fw-bold">10</span> {{-- $followedWriters->count() ?? 0 --}}
                                    <span class="text-muted small">Following</span>
                                </div>
                                <div class="me-4">
                                    <span class="fw-bold">11</span> {{-- $user->reactions->count() ?? 0 --}}
                                    <span class="text-muted small">Reactions</span> 
                                </div>
                                <div>
                                    <span class="fw-bold">12</span> {{-- $user->comments->count() ?? 0 --}}
                                    <span class="text-muted small">Comments</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right: Edit Button -->
                        <div class="d-flex flex-column align-items-end">
                            <a href="#" class="btn btn-lg btn-subscribe mb-2" 
                               onclick="document.getElementById('info-tab').click();">
                                <i class="fas fa-edit me-1"></i> Edit Profile
                            </a>

                            <a href="#" class="btn btn-lg secondary-btn" 
                               onclick="document.getElementById('info-tab').click();">
                                <i class="fas fa-edit me-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        <x-taps :tabs="$profileTabs" :activeTab="$activeTab" />
        
        <div class="card">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="Reactions-tab" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                    <div class="card border-ligt p-2 ">
                        <form action="" method="post" id="readerForm">              
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{$user->name}}">
                            </div>
        
                            <div class="mb-3">
                                <label for="profile_email" class="form-label">Email</label>
                                <input type="email" name="profile_email" id="profile_email" class="form-control" value="{{$user->email}}">
                            </div>
        
                            <div class="mb-3">
                                <label for="profile_password" class="form-label">Old Password</label>
                                <input type="password" name="profile_password" id="profile_password" class="form-control" placeholder="Enter your Current Password">
                            </div>
        
                            <div class="mb-3">
                                <label for="profile_NewPassword" class="form-label">New Password</label>
                                <input type="password" name="profile_NewPassword" id="profile_NewPassword" class="form-control" placeholder="Leave empty to keep current password">
                            </div>
        
                            <div class="d-flex justify-content-end">
                                <button type="submit" name="updateReader" class="btn btn-subscribe fw-bold">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
        
                <div class="tab-pane fade " id="SavedContent" role="tabpanel" aria-labelledby="Saved-tab" tabindex="0">
                    <div class="card border-ligt p-2">
                        <div class="card-body mt-2">
                            <h5 class="card-title fw-bold m-2">Saved Articles</h5>
                            <div class="card-body mt-2 ">
                                <x-dashboard-table :vars="$savedArticles"/>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div class="tab-pane fade " id="FollowingContent" role="tabpanel" aria-labelledby="Following-tab" tabindex="0">
                    <div class="card border-ligt p-2">
                        <div class="card-body mt-2 ">
                            <h5 class="card-title fw-bold m-2">writers you follow</h5>
                            <footer class="text-muted">the writers that you follow will be able to send to you a weekly/monthly newsletter in thar niche</footer>
                        
                            <div class="card-body mt-2">
                                <x-dashboard-table :vars="$followedWriters"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection