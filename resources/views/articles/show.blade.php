@extends('layouts.app')
@section('title', $article->title)
@section('content')
<section class="container-flued p-lg-5 mx-lg-5 d-flex flex-column justify-content-center align-items-center">
    <div class="px-lg-5 mx-5">
        {{-- the article title --}}
        <div class="pb-2" id="title">
            <span class="badge mb-2 text-start cata-bg fw-bold">{{ $article->categorie->name ?? 'Uncategorized' }}</span>
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
                @auth
                    @if(Auth::id() == $article->author->user_id)
                        {{-- Buttons for the article author --}}
                        <div class="d-flex align-items-center">
                            <div>
                                <x-user_avatar :user="$article->author->user" class="me-3" />
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-bold z-3 display-block mb-1">{{ $article->author->user->name }}</span>
                                <span class="text-muted small">You are the author of this article.</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('articles.edit', $article->article_id) }}" class="btn secondary-btn btn-sm me-2 rounded-pill">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('articles.destroy', $article->article_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" class="me-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                            {{-- Add your "Send as Newsletter" button/form here --}}
                            {{-- Example:{{ route('articles.sendNewsletter', $article->article_id) }} --}}
                            <form action="#" method="POST" onsubmit="return confirm('Are you sure you want to send this article as a newsletter?');">
                                @csrf
                                <button type="submit" class="btn btn-subscribe-outline   btn-sm rounded-pill">
                                    <i class="fas fa-paper-plane me-1"></i> Send as Newsletter
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- Standard author info and interaction buttons for other logged-in users --}}
                        <div class="d-flex align-items-center">
                            <div>
                                <x-user_avatar :user="$article->author->user" class="me-3" />
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{route('profile.show',$article->author->profile_id)}}" class="fw-bold z-3 display-block mb-2">{{ $article->author->user->name }}</a>
                                <span class="text-muted overlay-hide" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; max-height: 2em; line-height: 1.8em; max-width: 250px;">{{ $article->author->bio ?? 'No bio available' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @php
                                $isFollowing = Auth::user()->isFollowing($article->author);
                                $isLiked = Auth::user()->hasLiked($article); // You'll need this method
                                $isSaved = Auth::user()->hasSaved($article); // You'll need this method
                            @endphp
                            <button type="button" class="btn {{ $isFollowing ? 'secondary-btn' : 'btn-outline-secondary' }} rounded-pill me-2 js-follow-button"
                                    data-profile-id="{{ $article->author->profile_id }}"
                                    data-is-following="{{ $isFollowing ? 'true' : 'false' }}"
                                    data-follow-url="{{ route('interactions.profiles.follow', $article->author->profile_id) }}"
                                    data-unfollow-url="{{ route('interactions.profiles.unfollow', $article->author->profile_id) }}">
                                {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                            </button>
                            
                            {{-- Like and Save buttons for authenticated users --}}
                            <div class="d-flex flex-row align-items-center">
                                <button type="button" class="p-2 border-0 bg-transparent" id="likeButton"
                                        data-liked="{{ $isLiked ? 'true' : 'false' }}"
                                        data-like-url="{{ route('articles.like', $article->article_id) }}"
                                        data-unlike-url="{{ route('articles.unlike', $article->article_id) }}">
                                    <i class="like-icon {{ $isLiked ? 'fa-solid' : 'fa-regular' }} fa-heart" style="color: #ff8787;"></i>
                                    <span class="like-count small text-muted">{{ $article->like_count ?? 0 }}</span>
                                </button>
                                <button type="button" class="p-2 border-0 bg-transparent" id="saveArticleButton"
                                        data-saved="{{ $isSaved ? 'true' : 'false' }}"
                                        data-save-url="{{ route('articles.save', $article->article_id) }}"
                                        data-unsave-url="{{ route('articles.unsave', $article->article_id) }}"
                                        title="{{ $isSaved ? 'Unsave article' : 'Save article' }}">
                                    <i class="save-icon {{ $isSaved ? 'fa-solid' : 'fa-regular' }} fa-bookmark" style="color: #0d6efd;"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Author info for guests --}}
                    <div class="d-flex align-items-center">
                        <div>
                            <x-user_avatar :user="$article->author->user" class="me-3" />
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{route('profile.show',$article->author->profile_id)}}" class="fw-bold z-3 display-block mb-2">{{ $article->author->user->name }}</a>
                            <span class="text-muted overlay-hide" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; max-height: 2em; line-height: 1.8em; max-width: 250px;">{{ $article->author->bio ?? 'No bio available' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill me-2">
                            Follow
                        </a>
                        <div class="d-flex flex-row align-items-center">
                            <div class="border-start p-2 border-2 border-black">
                                <i class="fa-regular fa-heart" style="color: #ff8787;"></i>
                                <span class="like-count small text-muted">{{ $article->like_count ?? 0 }}</span>
                            </div>
                            <a href="{{ route('login') }}" class="p-2" title="Login to save">
                                <i class="fa-regular fa-bookmark" style="color: #0d6efd;"></i>
                            </a>
                        </div>
                    </div>
                @endauth
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
            :url="route('articles', ['q' => $article->author->user->name ?? $article->categorie->name ?? ''])" />
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
    </script>
@endpush