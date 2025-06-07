@extends('layouts.app')
@section('title', 'Profile')
@section('content')
@php 
    // dd('$savedArticles'); 
@endphp
<section class="container-fluid p-3">
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
                                                <p class="text-muted mt-1" style="font-size: 0.85rem;">Feel Free To edit Your Profile</p> {{-- Changed to <p>, adjusted style --}}
                                            </div>
                                            
                                            {{-- Stats Section --}}
                                            <div class="d-flex justify-content-around pt-3 mt-3 border-top"> {{-- Added padding-top, margin-top, and border-top --}}
                                                {{-- Stat Item 1: Articles Read --}}
                                                <div class="text-center px-2"> {{-- Added horizontal padding --}}
                                                    <h5 class="mb-0" style="font-weight: 700;">24</h5>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Following</small> 
                                                </div>
                                                {{-- Stat Item 2: Writers Following --}}
                                                <div class="text-center px-2"> {{-- Added horizontal padding --}}
                                                    <h5 class="mb-0" style="font-weight: 700;">3</h5>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Saved Article</small>
                                                </div>
                                                {{-- Stat Item 3: Reactions --}}
                                                <div class="text-center px-2"> {{-- Added horizontal padding --}}
                                                    <h5 class="mb-0" style="font-weight: 700;">12</h5>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Reactions</small>
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
                        <form action="#" method="#" class="w-100">
                            @csrf
                            <div class="row g-3">
                                {{-- name --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label mt-2">Your Full Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="name"
                                            id="name" placeholder="Enter Your New Full Name" aria-required="true" 
                                            value="{{ $user_name }}" required>
                                    <x-error_msg field="name"/>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label for="email" class="form-label mt-2">Your Email<span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-sm" name="email"
                                            id="email" placeholder="Enter Your New Email" aria-required="true" 
                                            value="{{ $user_email }}" required>
                                    <x-error_msg field="email"/>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label mt-2">Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm" id="password" 
                                                placeholder="Enter your newPassword" aria-required="true" name="password"
                                                autocomplete="new-password" required>
                                        <x-visibility-toggle/>
                                    </div>
                                    <footer class="text-muted">Must be at least 8 characters</footer>
                                    <x-error_msg field="password"/>
                                </div>
                        
                                <!-- confirm password -->
                                <div class="col-md-6">
                                    <label for="Subpassword_confirmation" class="form-label mt-2">Confirm Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm" id="Subpassword_confirmation" name="Subpassword_confirmation" 
                                                placeholder="Enter your Confirmation" aria-required="true" autocomplete="new-password" required>
                                        <x-visibility-toggle/>
                                    </div>
                                    <footer class="text-muted">Must be at least 8 characters</footer>
                                    <x-error_msg field="Subpassword_confirmation"/>
                                </div>
                                <div class="d-flex align-items-center justify-content-end">
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

                        {{-- Writer Item 1 --}}
                        <div class="position-relative">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                <div class="me-3">
                                    <x-user_avatar :user="(object)['name' => 'Sarah Johnson', 'profile' => null]" />
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="stretched-link fw-bold display-7 me-2 text-decoration-none">Sarah Johnson</a><br>
                                    <small class="text-muted">Following since April 10, 2025</small>
                                </div>
                                <!-- Button needs to be outside the stretched-link container or with higher z-index -->
                            </div>
                            <!-- Move button outside the stretched-link container -->
                            <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-end">
                                        <span class="d-block text-muted" style="font-size: 14px;">42 articles</span>
                                        <span class="d-block text-muted" style="font-size: 12px;">8 newsletters</span>
                                    </div>
                                    <button class="btn secondary-btn btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unfollow</button>
                                </div>
                            </div>
                        </div>

                        {{-- Writer Item 2 --}}
                        <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                            {{-- Use the user_avatar component --}}
                            <div class="me-3">
                                <x-user_avatar :user="(object)['name' => 'Alex Chen', 'profile' => null]" />
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1" style="font-weight: 600; color: #333;">Alex Chen</h5>
                                <small class="text-muted">Following since March 25, 2025</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="me-4 text-muted" style="font-size: 14px;">29 articles</span>
                                <button class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unfollow</button>
                            </div>
                        </div>

                        {{-- Writer Item 3 --}}
                        <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                            {{-- Use the user_avatar component --}}
                            <div class="me-3">
                                <x-user_avatar :user="(object)['name' => 'Michael Rodriguez', 'profile' => null]" />
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1" style="font-weight: 600; color: #333;">Michael Rodriguez</h5>
                                <small class="text-muted">Following since April 5, 2025</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="me-4 text-muted" style="font-size: 14px;">36 articles</span>
                                <button class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unfollow</button>
                            </div>
                        </div>

                        {{-- When using real data, you would do something like this: --}}
                        {{-- @foreach($followedWriters as $writer)
                        <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                            <div class="me-3">
                                <x-user_avatar :user="$writer" />
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1" style="font-weight: 600; color: #333;">{{ $writer->name }}</h5>
                                <small class="text-muted">Following since {{ $writer->followed_since }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="me-4 text-muted" style="font-size: 14px;">{{ $writer->articles_count }} articles</span>
                                <button class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unfollow</button>
                            </div>
                        </div>
                        @endforeach --}}
                    </div>
                </div>

                {{-- Saved Tab Pane --}}
                <div class="tab-pane fade" id="Saved" role="tabpanel" aria-labelledby="Saved-tab">
                    <div class="container py-4">
                        <h3 class="mb-4" style="font-weight: 600; color: #333;">Saved Articles</h3>

                        {{-- Saved Article Item 1 --}}
                        <div class="position-relative">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">

                                <div class="flex-grow-1">
                                    <a href="#" class="stretched-link fw-bold display-7 me-2 text-decoration-none">The Future of Web Development: AI and Beyond</a><br>
                                    <small class="text-muted">By Sarah Johnson • Saved on April 15, 2025</small>
                                </div>
                            </div>
                            <!-- Move button outside the stretched-link container -->
                            <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-end">
                                        <span class="d-block text-muted" style="font-size: 14px;">5 min read</span>
                                        <span class="d-block text-muted" style="font-size: 12px;">Technology</span>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unsave</button>
                                </div>
                            </div>
                        </div>

                        {{-- Saved Article Item 2 --}}
                        <div class="position-relative">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                <div class="me-3">
                                    <x-user_avatar :user="(object)['name' => 'Alex Chen', 'profile' => null]" />
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="stretched-link fw-bold display-7 me-2 text-decoration-none">Understanding Modern JavaScript Frameworks</a><br>
                                    <small class="text-muted">By Alex Chen • Saved on April 12, 2025</small>
                                </div>
                            </div>
                            <!-- Move button outside the stretched-link container -->
                            <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-end">
                                        <span class="d-block text-muted" style="font-size: 14px;">8 min read</span>
                                        <span class="d-block text-muted" style="font-size: 12px;">Programming</span>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Remove</button>
                                </div>
                            </div>
                        </div>

                        {{-- Saved Article Item 3 --}}
                        <div class="position-relative">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                <div class="me-3">
                                    <x-user_avatar :user="(object)['name' => 'Michael Rodriguez', 'profile' => null]" />
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="stretched-link fw-bold display-7 me-2 text-decoration-none">Building Scalable Newsletter Systems</a><br>
                                    <small class="text-muted">By Michael Rodriguez • Saved on April 8, 2025</small>
                                </div>
                            </div>
                            <!-- Move button outside the stretched-link container -->
                            <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-end">
                                        <span class="d-block text-muted" style="font-size: 14px;">12 min read</span>
                                        <span class="d-block text-muted" style="font-size: 12px;">Business</span>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Remove</button>
                                </div>
                            </div>
                        </div>

                        {{-- Saved Article Item 4 --}}
                        <div class="position-relative">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                <div class="me-3">
                                    <x-user_avatar :user="(object)['name' => 'Emily Davis', 'profile' => null]" />
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="stretched-link fw-bold display-7 me-2 text-decoration-none">Design Trends That Will Shape 2025</a><br>
                                    <small class="text-muted">By Emily Davis • Saved on April 5, 2025</small>
                                </div>
                            </div>
                            <!-- Move button outside the stretched-link container -->
                            <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-end">
                                        <span class="d-block text-muted" style="font-size: 14px;">6 min read</span>
                                        <span class="d-block text-muted" style="font-size: 12px;">Design</span>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Remove</button>
                                </div>
                            </div>
                        </div>

                        {{-- When using real data, you would do something like this: }}
                        {{-- @foreach($savedArticles as $article)
                        <div class="position-relative">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                <div class="me-3">
                                    <x-user_avatar :user="$article->author" />
                                </div>
                                <div class="flex-grow-1">
                                    <a href="{{ route('articles.show', $article->id) }}" class="stretched-link fw-bold display-7 me-2 text-decoration-none">{{ $article->title }}</a><br>
                                    <small class="text-muted">By {{ $article->author->name }} • Saved on {{ $article->saved_at->format('M j, Y') }}</small>
                                </div>
                            </div>
                            <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-end">
                                        <span class="d-block text-muted" style="font-size: 14px;">{{ $article->reading_time }} min read</span>
                                        <span class="d-block text-muted" style="font-size: 12px;">{{ $article->category }}</span>
                                    </div>
                                    <form action="{{ route('articles.unsave', $article->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
