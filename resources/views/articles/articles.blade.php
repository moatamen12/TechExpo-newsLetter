@extends('layouts.app')
@section('title', 'Articles') 
@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<!-- Breadcrumb Navigation > -->
<section class=" container animate-fadein">
    <div class="my-2 p-3">
        <div class="my-3"> 
            @include('components.sub_header')    
        </div>

        <div class="row">
            <div class="col-md-12 px-4" id="articles-container ">
                @forelse ($articles as $article)
                    <!-- used in the artical card viewing -->
                    <div class="card rounded-4 p-4 my-4 articall-card-hover border-1 border-light-subtle ">
                        <div class="row g-3">
                            <div class="col-lg-5 d-flex flex-column">
                                <!-- Categories -->
                                <spam href="#" class="badge mb-2 text-start w-50"><i class="fas fa-circle me-2 small fw-bold"></i>{{$article->categorie->name }}</spam>
                                <!-- Title -->
                                <h2 class="card-title">
                                    <a href="{{route('articles.show',$article->article_id)}}" class="btn-link text-reset stretched-link">{{$article->title}}</a>
                                </h2>

                                <!-- Author info -->
                                <div class="d-flex align-items-center position-relative mt-auto">
                                    <div class="avatar me-2">
                                        <x-user_avatar :user="$article->author->user" />  {{-- for displaying the user profile photo --}}
                                    </div>
                                    <div>
                                        {{-- <h5 class="mb-0"><a href="#" class="text-reset btn-link">{{$article->author_name}}</a></h5> --}}
                                        <div>
                                            <ul class="nav align-items-center small text-decoration-none">
                                                <li class="nav-item me-3">
                                                    <span class="ms-2">
                                                        <a href="#" class="text-reset btn-link">
                                                            @if ($article->author && $article->author->user)
                                                                {{ $article->author->user->name }}
                                                            @endif
                                                        </a>
                                                    </span>
                                                </li>
                                            </ul>
                                            <ul class="nav align-items-center small text-decoration-none mt-2">
                                                <li class="nav-item me-3">{{ $article->published_at->format('M d, Y') }}</li>
                                                <li class="nav-item">
                                                    <x-reading_time :article="$article" />
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Detail -->
                            <div class="col-md-6 col-lg-4">
                                <p>{{$article->summary}} </p>
                            </div>
                            <!--article Image -->
                            <div class="col-md-6 col-lg-3">
                                <div class="article-image-container">
                                    @if($article->featured_image_url && 
                                        Storage::disk('public')->exists($article->featured_image_url) && 
                                        !str_contains($article->featured_image_url, 'test.jpg'))
                                        <img class="rounded-3 article-image" 
                                             src="{{ asset('storage/' . $article->featured_image_url) }}" 
                                             alt="{{$article->title}}"
                                             loading="lazy">
                                    @else
                                        <x-article-image :imageURL="$article->featured_image_url" />
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- No articles found -->
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                        <x-no-articles-found />
                        {{-- <p class="text-center fs-4 text-muted">No articles found.</p> --}}
                    </div>
                @endforelse
            </div>
        </div>
        {{-- pagination --}}
        <div class="d-flex justify-content-center align-item-center my-3">
            <div class="text-body-secondary">
                {{$articles->links('pagination::bootstrap-5')}}
            </div>
        </div>
    </div>
</section>



@endsection