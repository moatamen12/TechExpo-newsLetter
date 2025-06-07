<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@hasSection('title')@yield('title') - Tech Expo @else Tech Expo @endif</title>

        <script src="https://kit.fontawesome.com/fc7e8d802d.js" crossorigin="anonymous"></script>  {{-- Font Awesome icons --}}
        {{-- Bootstrap include --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
              integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
              
        {{-- Custom CSS --}}
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">  

        <!-- favicon -->
        <link rel="apple-touch-icon"          sizes="180x180" href="{{asset('assets/favicon/apple-touch-icon.png')}}">
        <link rel="icon"     type="image/png" sizes="96x96"   href="{{asset('assets/favicon/favicon-96x96.png')}}"/>
        <link rel="icon"     type="image/png" sizes="192x192" href="{{asset('assets/favicon/web-app-manifest-192x192.png')}}">
        <link rel="icon"     type="image/png" sizes="512x512" href="{{asset('assets/favicon/web-app-manifest-512x512.png')}}">
        <link rel="icon"     type="image/png" sizes="any"     href="{{asset('assets/favicon/favicon.ico')}}">
        <link rel="icon"     type="image/svg+xml"             href="{{asset('assets/favicon/favicon.svg')}}" />
        <link rel="manifest"                                  href="{{asset('assets/favicon/site.webmanifest')}}">  {{-- mainafist file --}}     
    </head>   
     
    <body class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-custom sticky-top navbar-expand-lg border-bottom shadow-sm px-2">
            <div class="container-fluid">
                {{-- logo --}}
                <a class="navbar-brand logo fw-bold d-flex align-items-center" href="{{ route('home') }}">
                    <div class="logo">
                        <span class="text-3xl mr-1 r">⟨</span>TechExpo<span class="text-3xl ml-1">/⟩</span>
                    </div>
                </a>

                {{-- Offcanvas Toggler (Visible on small screens only) --}}
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#mainSidebar" aria-controls="mainSidebar" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- Navbar content for Large Screens (LG and up) --}}
                {{-- This div replaces the old #navbarSupportedContent --}}
                <div class="collapse navbar-collapse" id="navbarNavLargeScreen">
                    {{-- Centered Nav Links --}}
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        @include('layouts.partials._nav_links')
                    </ul>

                    {{-- Right-aligned Auth/Guest buttons --}}
                    <div class="d-flex align-items-center">
                        @include('layouts.partials._auth_section', ['context' => 'navbar'])
                    </div>
                </div>
            </div>
        </nav>

        <!-- Offcanvas Sidebar for Small Screens -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mainSidebar" aria-labelledby="mainSidebarLabel" data-bs-scroll="true">
            <div class="offcanvas-header border-bottom">
                <h5 class="navbar-brand offcanvas-title logo fw-bold" id="mainSidebarLabel">
                    <span class="text-3xl mr-1 r">⟨</span>TechExpo<span class="text-3xl ml-1 ">/⟩</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                {{-- Nav Links for Offcanvas --}}
                <ul class="navbar-nav flex-column mb-4"> {{-- mb-4 for spacing --}}
                    @include('layouts.partials._nav_links')
                </ul>

                {{-- Auth/Guest for Offcanvas (at the bottom) --}}
                <div class="mt-auto border-top pt-3"> {{-- mt-auto pushes to bottom --}}
                    @include('layouts.partials._auth_section', ['context' => 'offcanvas'])
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- footer-->
        <footer class="footer-bg py-2 mt-auto mt-5 border" >           
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="navbar-brand logo fw-bold d-flex align-items-center mb-2">
                            <span class="fs-4 me-1 fw-bold">&lt;</span>TechExpo<span class="fs-4 ms-1">/&gt;</span>
                        </div>
                        <p class="text-color">Your trusted source for the latest tech news, trends, and insights. Stay informed with our curated content.</p>
                        <div class="d-flex">
                            <ul class="list-unstyled"></ul>
                        </div>

                        <div >
                            <ul class="list-unstyled text-color">
                                <li><i class="fas fa-envelope me-2"></i> info@technewsletter.com</li>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}" class="fw-bold lk text-color text-decoration-none">Home</a></li>
                            <li><a href="{{ route('articles') }}" class="fw-bold lk text-color text-decoration-none">Articles</a></li>
                            <li><a href="{{ route('contact') }}" class="fw-bold lk text-color text-decoration-none">Contact</a></li>
                            <li><a href="{{ route('about') }}" class="fw-bold lk text-color text-decoration-none">About</a></li> 
                            {{-- <li><a href="#" data-bs-toggle="modal" data-bs-target="#subscribeModal" class="text-color text-decoration-none">Subscribe</a></li> --}}
                        </ul>
                    </div>
                    
                    {{-- footer btns --}}
                    <div class="col-md-3 mb-3"> 
                        <h5>Subscribe and join our community </h5>
                        <div class="d-grid gap-2 d-md-flex flex-column justify-content-md-start mt-4"> 
                            <a href="#" class="btn btn-subscribe btn-sm px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                                <i class="fas fa-envelope-open me-1"></i> Subscribe Now
                            </a>
                            <a href="{{ route('articles') }}" class="btn btn-subscribe-outline btn-sm rounded-3 px-4 py-2 mt-2"> {{-- Added margin-top for spacing --}}
                                <i class="fas fa-pen me-1"></i> Start Writing
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3 border-top border-secondary">
                <p class="text-color mb-0">&copy; 2025 Tech Newsletter. All rights reserved.</p>
            </div>
        </footer>


{{-- bootstrape --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous">
</script>

{{-- <script src="{{asset('assets/js/like.js')}}"></script> for the like btn --}}

@stack('scripts') {{-- Make sure this line exists --}}

</body>
</html>