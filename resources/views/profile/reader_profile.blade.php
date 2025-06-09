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
                                <span class="text-muted mt-5">pleas enter your pasword and its confirmation when editing your profile</span>
                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label mt-2">Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm" id="password" 
                                                placeholder="Enter your New Password" aria-required="true" name="password"
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
                                    {{-- <footer class="text-muted">Must be at least 8 characters</footer> --}}
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
            </div> 
        </div>
    </div>
</section>
<script src="{{ asset('assets/js/visability.js') }}"></script>
@endsection
