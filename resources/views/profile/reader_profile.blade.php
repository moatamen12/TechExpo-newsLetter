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
                        
                        @if($followedWriters && $followedWriters->count() > 0)
                            @foreach($followedWriters as $followedRelation)
                                @php
                                    $writer = $followedRelation->followed; 
                                @endphp
                                @if($writer)
                                <div class="position-relative">
                                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: #f1f1f1 !important;">
                                        <div class="me-3">
                                            {{-- Pass the User model of the writer to the avatar component --}}
                                            <x-user_avatar :user="$writer" /> 
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#" class="stretched-link fw-bold display-7 me-2 text-decoration-none">{{ $writer->name ?? 'Writer Name Unavailable' }}</a><br>
                                            {{-- <small class="text-muted">Following since N/A</small>  --}}
                                        </div>
                                    </div>
                                    <div class="position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;">
                                        <div class="d-flex align-items-center">
                                            {{-- <div class="me-3 text-end">
                                                <span class="d-block text-muted" style="font-size: 14px;">
                                                    {{ $writer->userProfile->num_articles ?? 0 }} articles
                                                </span>
                                                Newsletter count is not directly available, using placeholder
                                                <span class="d-block text-muted" style="font-size: 12px;">N/A newsletters</span>
                                            </div> --}}
                                            {{-- Replace {{ route('writers.unfollow', $writer->user_id) }} with the actual unfollow route --}}
                                            <form action="#" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE') {{-- Or POST, depending on your route definition --}}
                                                <button type="submit" class="btn secondary-btn btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unfollow</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <p>You are not following any writers yet.</p>
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
                                                {{-- {{ route('articles.unsave', $savedItem->article->article_id) }} --}}
                                                <form action="#" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px; font-weight: 500;">Unsave</button>
                                                </form>
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
@endsection
