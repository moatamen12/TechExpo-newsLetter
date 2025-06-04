<section class="intro-bg position-relative animate-fadein p-4">
    <div class="container">
        {{-- <!-- Featured Article - Big Card --> --}}
        @if($featuredArticle)
        <div class="card rounded-4 p-4 mt-5 border-1 border-secondary">
            <div class="row g-3">
                {{-- <!-- Article Image (Left Side) --> --}}
                <div class="col-md-4">
                    <div style="height: 100%; min-height: 300px; overflow: hidden;">
                        <x-article-image :imageURL='$featuredArticle->featured_image_url' />
                    </div>
                </div>
                
                {{-- <!-- Content (Right Side) --> --}}
                <div class="col-md-8">
                    {{-- <!-- Category & Title --> --}}
                    <div class="mb-3">
                        <span class="badge mb-2 text-start ">
                            <i class="fas fa-circle me-2 small fw-bold"></i>{{ $featuredArticle->categorie_name }}
                        </span>
                        <h2 class="card-title">
                            <a href="{{ route('articles.show', $featuredArticle->article_id) }}" 
                               class="btn-link text-reset stretched-link">{{ $featuredArticle->title }}</a>
                        </h2>
                    </div>
                    
                    {{-- <!-- Article Summary --> --}}
                    <div class="mb-4">
                        <p>{{ $featuredArticle->summary }}</p>
                    </div>  
                    <!-- Author Info -->
                    <div class="d-flex align-items-center">
                        <div>
                            <ul class="nav align-items-center small text-decoration-none">
                                <li class="nav-item ">
                                    <x-user_avatar :user="$featuredArticle->author->user" />  {{-- for displaying the user profile photo --}}
                                </li>

                                <li class="nav-item me-3">
                                    <span class="ms-2 small">
                                        <a href="#" class="text-reset btn-link">
                                            @if ($featuredArticle->author && $featuredArticle->author->user)
                                                {{ $featuredArticle->author->user->name }}
                                            @endif
                                        </a>
                                    </span>
                                </li>
                            </ul>
                            <ul class="nav align-items-center small text-decoration-none mt-2">
                                <li class="nav-item me-3">{{ $featuredArticle->published_at->format('M d, Y') }}</li>
                                <li class="nav-item">
                                    <x-reading_time :article="$featuredArticle" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
