@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <section class="py-5 bg-gradient-primary animate-fadein hero-bg">
        <div class="container">
            <div class="row align-items-center justify-content-center min-vh-60">
                <div class="col-lg-8 text-center text-white">
                    <h1 class="display-3 fw-bold mb-4">
                        Tech And Science, <br>All in Your Inbox! 
                        <i class="fas fa-paper-plane ms-2"></i>
                    </h1>
                    <p class="lead mb-4 px-md-5 fs-5 opacity-90">
                        Curated tech insights and breakthrough discoveries delivered weekly by passionate experts in your field.
                    </p>
                    @guest {{--if the user is a guest "NOT AUTHED"--}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center my-4">
                            <a class="btn btn-subscribe btn-lg px-4 py-3 rounded-pill Zbtn shadow-lg" href="{{route('login')}}">
                                <i class="fas fa-envelope-open me-2"></i> Subscribe Now
                            </a>
                            <a class="btn btn-subscribe-outline btn-lg px-4 py-3 rounded-pill Zbtn shadow-lg" style="color:#e2ebf4 !important" href="#">
                                <i class="fas fa-pen me-2"></i> Start Writing
                            </a>
                        </div>
                    @endguest
                    @auth{{--if the user is a Writer --}}
                        @if(Auth::user()->userProfile()->exists())

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center my-4">
                                <a class="btn btn-subscribe btn-lg px-4 py-3 rounded-pill Zbtn shadow-lg" href="{{route('dashboard')}}">
                                    Manage Your Content
                                </a>
                                <a class="btn btn-subscribe-outline btn-lg px-4 py-3 rounded-pill Zbtn shadow-lg" style="color:#e2ebf4 !important" href="#">{{--{{route('author_profile')}}--}}
                                    <i class="fa-regular fa-user"></i> Profile Settings
                                </a>
                            </div>

                        @else{{--if the user is a Reader --}}
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center my-4">
                                <a class="btn btn-subscribe btn-lg px-4 py-3 rounded-pill Zbtn shadow-lg" href="{{route('articles')}}">
                                    <i class="fas fa-envelope-open me-2"></i> View Articles and News
                                </a>
                                <a class="btn btn-subscribe-outline btn-lg px-4 py-3 rounded-pill Zbtn shadow-lg"       style="color:#e2ebf4 !important" href="#">
                                    <i class="fas fa-pen me-2"></i> Start Writing
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Today's Top Highlights -->
    @include('home.partials.top-highlights')    
        
    <!-- Latest Articles -->
    <div class="my-5">
        @include('home.partials.latest-articles')
    </div>
    <x-subscribe-footer-card />
@endsection