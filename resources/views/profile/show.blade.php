@extends('layouts.app')
@section('title', $author->name . ' - Profile')
@section('content')
<div class="container my-5">
    {{-- Author Profile Card --}}
    <div class="card shadow-sm mb-4 overflow-hidden"> {{-- Added overflow-hidden to contain the image --}}
        <div class="row g-0">
            <div class="col-md-4"> {{-- Adjusted column size for a larger image area --}}
                @if($author->userProfile && $author->userProfile->profile_photo)
                    <img src="{{ asset('storage/' . $author->userProfile->profile_photo) }}" 
                         alt="{{ $author->name }}'s profile photo" 
                         class="img-fluid w-100 h-100" 
                         style="object-fit: cover; height: 300px; max-height: 300px;"> {{-- Limited height with max-height --}}
                @else
                    {{-- Fallback if no profile photo is available --}}
                    <div class="d-flex align-items-center justify-content-center bg-light w-100" style="height: 300px;">
                        <span class="text-muted">No Photo</span>
                    </div>
                @endif
            </div>
            <div class="col-md-8"> {{-- Adjusted column size for content --}}
                <div class="card-body p-4 position-relative" style="min-height: 300px;"> {{-- Added position-relative and min-height --}}
                    <h4>{{ $author->name }}</h4>
                    @if($author->userProfile && $author->userProfile->title)
                        <p class="text-muted">{{ $author->userProfile->title }}</p>
                    @endif

                    @if($author->email)
                        <p class="mb-2"><strong>Email:</strong> <a href="mailto:{{ $author->email }}">{{ $author->email }}</a></p>
                    @endif
                    
                    <h5 class="mt-3">Bio</h5>
                    <p>{{ $author->userProfile->bio ?? 'No bio provided.' }}</p>

                    @auth {{-- Only show if user is logged in --}}
                        @if(auth()->user()->id !== $author->id) {{-- Don't show follow for own profile --}}
                            {{-- Check if current user is following this author --}}
                            @php
                                $isFollowing = auth()->user()->isFollowing($author->id); // Ensure this method exists on your User model
                            @endphp
                            <form action="{{ $isFollowing ? route('users.unfollow', $author->id) : route('users.follow', $author->id) }}" method="POST" class="mt-3">
                                @csrf
                                @if($isFollowing)
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Unfollow</button>
                                @else
                                    <button type="submit" class="btn btn-primary">Follow</button>
                                @endif
                            </form>
                        @endif
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary mt-3">Login to Follow</a>
                    @endguest

                    {{-- Social Media Links - Positioned at bottom right --}}
                    @if($author->userProfile && $author->userProfile->activeSocialLinks->count() > 0)
                        <div class="position-absolute bottom-0 end-0 p-3">
                            <div class="d-flex flex-column align-items-end">
                                {{-- <small class="text-muted mb-2">Connect:</small> --}}
                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                    @foreach($author->userProfile->activeSocialLinks as $socialLink)
                                        <a href="{{ $socialLink->url }}" target="_blank" 
                                           class="btn secondary-btn btn-sm" 
                                           title="{{ ucfirst($socialLink->platform) }}"
                                           style="color: {{ $socialLink->platform_color ?? '#6c757d' }}; border-color: {{ $socialLink->platform_color ?? '#6c757d' }};">
                                            <i class="{{ $socialLink->platform_icon ?? 'fas fa-link' }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Author's Articles --}}
    <div class="mt-5">
        <x-headers title="Articles by {{ $author->name }}" 
            description="Browse through the articles written by {{ $author->name }}."
            url="{{ route('articles') }}?q={{ urlencode($author->name) }}" 
            text="See All Articles by {{ $author->name }}" />
        <hr>
        @if($articles && $articles->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4"> {{-- Using Bootstrap grid for layout --}}
                @foreach($articles as $article)
                    {{-- @php dd($article); @endphp --}}
                    {{-- Each article card --}}

                    {{-- </div> --}}
                    <div class="col"> {{-- Each card in a column --}}
                        <x-card_vertical :article="$article" />
                    </div>
                @endforeach
            </div>
        @else
            <p>{{ $author->name }} has not published any articles yet.</p>
        @endif
    </div>

    {{-- Remove or comment out the debug section --}}
    {{-- Debug: Check what social data is available
    @if($author->userProfile)
        @dump($author->userProfile->social_twitter)
        @dump($author->userProfile->social_linkedin)
        @dump($author->userProfile->social_github)
        @dump($author->userProfile->social_website)
        @dump($author->userProfile->activeSocialLinks)
    @endif --}}
</div>
@endsection