@props(['article'])

<div class="col border-1 border-light">
    <div class="card h-100  articall-card-hover">
        <!-- Card img -->
        <div class="position-relative" style="height: 200px; overflow: hidden; background-color: #e1e6eb;">
            <x-article-image :imageURL="$article->featured_image_url" />
        </div>
        <div class="mt-2 me-2 ms-2 d-flex justify-content-between align-items-center">
            <span class="badge bg-secondary text-uppercase mb-2">
                <i class="fas fa-circle me-2 small fw-bold"></i>{{$article->categorie->name}}
            </span>
            <span class="text-uppercase mb-2">
                <p class="nav-item small text-muted">{{ $article->published_at->format('M d, Y') }}</p> 
            </span>

        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-3"><a href="{{route('articles.show',$article->article_id)}}" class="text-reset fw-bold stretched-link">{{$article->title}}</a></h5>                             
            <p class="card-text mt-3">{{$article->summary}}</p>
        </div>    
        <!-- Card info -->
        <div class="card-footer">
            <ul class="nav d-flex align-items-center justify-content-between mt-auto"> 
                <li class="nav-item me-2">
                    <div class="d-flex align-items-center position-relative">
                        <x-user_avatar :user="$article->author->user" />
                        <span class="ms-2 small">
                            <a href="{{ route('profile.show', $article->author->profile_id) }}" 
                                class="text-reset btn-link fw-bold position-relative stretched-link"
                                style="z-index: 100;">
                                @if ($article->author && $article->author->user)
                                    {{ $article->author->user->name }}
                                @endif
                            </a>
                        </span> 
                    </div>
                </li>
                <li class="nav-item small text-muted"><x-reading_time :article="$article" /></li> 
            </ul>
        </div>
    </div>  
</div>