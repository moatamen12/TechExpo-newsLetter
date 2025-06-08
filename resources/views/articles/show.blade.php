@extends('layouts.app')
@section('title', $article->title)
@section('content')
<section class="container-flued p-lg-5 mx-lg-5 d-flex flex-column justify-content-center align-items-center">
    <div class="px-lg-5 mx-5">
        {{-- the article title --}}
        <div class="pb-2" id="title">
            <span class="badge mb-2 text-start cata-bg fw-bold">{{$article->categorie_name}}</span>
            <h1 class="text-start my-2 fw-bold">{{$article->title}}</h1>
            {{-- <h1 > {{$article->title}}</h1> --}}
            <div class="d-flex flex-row mt-3">
                <p class="text-muted">{{$article->updated_at->format('M d, Y') }}</p>
                {{-- clac the reading time of the article --}}
                <x-reading_time :article=$article class="mx-5"/>
            </div>
        </div>

        {{-- Featured Image --}}
        @if($article->featured_image_url)
        <div class="d-flex featured-image-container my-4 align-items-center justify-content-center">
            <img src="{{ asset('storage/' . $article->featured_image_url) }}" 
                 alt="{{ $article->title }}" 
                 class="img-fluid rounded featured-image"
                 loading="lazy">
        </div>
        @endif

        {{-- the auther info  --}}
        <div class="border-bottom border-top border-2 pb-2">
            <div class="d-flex align-items-center justify-content-between my-3">
                <div class="d-flex align-items-center">
                    <div>
                        @include('components.user_avatar', ['user' => $article->author])
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold display-block mb-2">{{ $article->author->user->name }}</span>
                        <span class="text-muted">{{ $article->UserProfiles->work ?? 'No bio available' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @auth
                        @if(Auth::id() != $article->author->user_id)
                            @php
                                $isFollowing = \App\Models\userFollower::where('follower_id', Auth::id())
                                    ->where('following_id', $article->author->profile_id)
                                    ->exists();
                            @endphp
                            <button class="btn btn-outline-secondary rounded-pill secondary-btn me-2 follow-button" 
                                    data-user-id="{{ $article->author->profile_id }}"
                                    data-following="{{ $isFollowing ? 'true' : 'false' }}">
                                {{ $isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill secondary-btn me-2">
                            Follow
                        </a>
                    @endauth
                    <div class="d-flex flex-column align-items-center">
                        {{-- Add data-article-id and check if liked by current user --}}
                        @php
                            $isLikedByCurrentUser = $article->isLikedByCurrentUser();
                        @endphp
                        @auth
                            <div id="likeButton" class="border-start p-2 border-2 border-black" 
                                 data-article-id="{{ $article->article_id }}" 
                                 style="cursor: pointer;"
                                 data-liked="{{ $isLikedByCurrentUser ? 'true' : 'false' }}">

                                <i class="like-icon {{ $isLikedByCurrentUser ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }}" style="color: #ff8787;"></i>
                                <span class="like-count small text-muted">{{ $article->like_count ?? 0 }}</span>
                            </div>
                        @else
                            <div class="border-start p-2 border-2 border-black">
                                <i class="fa-regular fa-heart" style="color: #ff8787;"></i>
                                <span class="like-count small text-muted">{{ $article->like_count ?? 0 }}</span>
                            </div>
                        @endauth
                    </div>
                </div>

            </div>  
        </div>

        {{-- articles contaent --}}
        <div class="my-5 border-bottom border-2 pb-2">
            <div class="article-content">
                {!! $article->content !!}
            </div>
        </div>

        {{-- commints only diplayed foe loged in users --}}
        @auth
            <div class="border rounded-3 p-3 bg-light my-5">
                <h2 class="mb-4"> Give us your opinion </h2>
                <form action="{{ route('comments.store', $article->article_id) }}" method="POST">
                    @csrf
                    <textarea class="form-control my-3 bg-white" id="commentTE" name="comment"
                            placeholder="Add your comment..."
                            maxlength="1000" required
                            style="resize: none; min-height: 200px; overflow: auto;"></textarea>
                    <footer class="text-muted text-start mt-2">Be respectful and constructive in your feedback(1000 char max)</footer>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-subscribe">Post Comment</button>
                    </div>
                </form>
            </div>
        @endauth
    </div>
</section>


{{-- display comments --}}
<div class="container my-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h3 class="mb-4 fw-bold">Comments ({{ $comments->total() }})</h3>
            @forelse($comments as $comment)
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-body p-4">
                      {{-- Parent comment --}}
                        <div class="d-flex mb-3 border-bottom">
                            {{-- User avatar --}}
                            <div class="me-3">
                                @if(isset($comment->user))
                                    @include('components.user_avatar', ['user' => $comment->user])
                                @else
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 48px; height: 48px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $comment->user->name }}</h5>
                                <p class="text-muted small mb-0">
                                    @if(isset($comment->created_at))
                                        <small>{{ \Carbon\Carbon::parse($comment->created_at)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($comment->created_at)->format('h:i A') }}</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="comment-content ps-5 ms-2 form-control my-3" 
                            style="resize: none; min-height: 100px; max-height: 200px; overflow: auto;">
                            <p class="mb-2">{{ $comment->content }}</p>
                        </div>
                        
                        {{-- Reply button --}}
                        <div class="text-end mt-2">
                            <button class="btn btn-sm secondary-btn rounded-pill " 
                                    onclick="toggleReplyForm('reply-form-{{ $comment->comment_id }}')" 
                                    type="button">
                                <i class="far fa-comment-dots me-1"></i> Reply
                            </button>
                        </div>
                        
                        {{-- Reply form --}}
                        <div id="reply-form-{{ $comment->comment_id }}" class="mt-3 ps-5 ms-2" style="display: none;">
                            <form action="{{ route('comments.store', $article->article_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->comment_id }}">
                                <div class="form-group">
                                    <textarea class="form-control" name="comment" rows="3" 
                                              placeholder="Write your reply..." required
                                              style="resize: none;"></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                            onclick="toggleReplyForm('reply-form-{{ $comment->comment_id }}')">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-subscribe">
                                        Post Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- Nested replies --}}
                        @if($comment->replies && $comment->replies->count() > 0)
                            <div class="nested-replies mt-4 ms-5 ps-3 border-start">
                                @foreach($comment->replies as $reply)
                                    <div class="reply mb-3 pb-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="me-2">
                                                @if(isset($reply->user))
                                                    @include('components.user_avatar', ['user' => $reply->user, 'size' => 32])
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px;">
                                                        <i class="fas fa-user fa-sm"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $reply->user->name }}</h6>
                                                <p class="text-muted small mb-2">
                                                    @if(isset($reply->created_at))
                                                        <small>{{ \Carbon\Carbon::parse($reply->created_at)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($reply->created_at)->format('h:i A') }}</small>
                                                    @endif
                                                </p>
                                                <div class="reply-content bg-light p-2 rounded">
                                                    {{ $reply->content }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-5 text-center">
                        <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                    </div>
                </div>
            @endforelse
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>

    {{-- related Articles --}}
    <div class="mt-5 mx-5">
        <x-headers               
            title="Related Articles"
            description="More Articles From {{ $article->author->user->name ?? 'this author' }}"
            :url="route('articles', ['q' => $article->author->user->name ?? $article->categorie_name])" />
    </div>
    <div class="row row-cols-sm-1 row-cols-lg-3 row-cols-md-2 g-4 m-4">
        @forEach($relatedArticles as $relatedArticle)
            <x-card_vertical :article="$relatedArticle" />
        @endforeach
    </div>
@endsection
@push('scripts')
<script>
    function toggleReplyForm(formId) {
        const form = document.getElementById(formId);
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Like button functionality
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById('likeButton');
        
        if (likeButton) {
            likeButton.addEventListener('click', function() {
                const articleId = this.getAttribute('data-article-id');
                const likeIcon = this.querySelector('.like-icon');
                const likeCount = this.querySelector('.like-count');
                
                // Disable button temporarily to prevent double clicks
                this.style.pointerEvents = 'none';
                
                fetch(`/articles/${articleId}/toggle-like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    // Update the like count
                    likeCount.textContent = data.like_count;
                    
                    // Update the heart icon and data attribute
                    if (data.liked) {
                        likeIcon.className = 'like-icon fa-solid fa-heart';
                        this.setAttribute('data-liked', 'true');
                    } else {
                        likeIcon.className = 'like-icon fa-regular fa-heart';
                        this.setAttribute('data-liked', 'false');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                })
                .finally(() => {
                    // Re-enable button
                    this.style.pointerEvents = 'auto';
                });
            });
        }

        // Follow button functionality
        const followButton = document.querySelector('.follow-button');
        
        if (followButton) {
            followButton.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                
                // Disable button temporarily to prevent double clicks
                this.style.pointerEvents = 'none';
                
                fetch(`/users/${userId}/toggle-follow`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    // Update the button text and class
                    if (data.followed) {
                        this.textContent = 'Following';
                        this.classList.add('following');
                        this.setAttribute('data-following', 'true');
                    } else {
                        this.textContent = 'Follow';
                        this.classList.remove('following');
                        this.setAttribute('data-following', 'false');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                })
                .finally(() => {
                    // Re-enable button
                    this.style.pointerEvents = 'auto';
                });
            });
        }
    });
</script>
@endpush