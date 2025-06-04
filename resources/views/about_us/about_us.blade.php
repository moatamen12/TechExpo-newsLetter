@extends('layouts.app')
@section('title', 'About Tech Expo')
@section('content')
<!-- Hero Section -->
<section class="m-5 py-5 text-center rounded text-white d-flex align-items-center" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('{{ asset('assets/images/about_us_bg.jpg') }}') center/cover no-repeat; min-height: 400px; ">
    <div class="container">
        <h1 class="display-4 fw-bold">About Tech Expo</h1>
        <p class="lead fs-4">Your trusted source for tech news, innovation, and scientific breakthroughs</p>
    </div>
</section>

<main class="container py-4">
    <!-- About / Mission Section -->
    <section class="my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="mb-4 fw-bold">Our Mission</h2>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <p class="fs-5">At Tech Expo, we're dedicated to bridging the gap between complex technology and everyday understanding. Our mission is to deliver high-quality, timely content that keeps you informed about the latest tech trends, scientific breakthroughs, and industry developments. We believe that technology should be accessible to everyone, and we strive to present information in a way that's both engaging and easy to comprehend.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Cover Section -->
    <section class="my-5 py-5 bg-light rounded">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h2 class="mb-4 fw-bold text-center">What We Cover</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-microchip text-primary fs-1 mb-3"></i>
                                <h5 class="fw-bold">Emerging Technologies</h5>
                                <p>AI, blockchain, quantum computing, and other cutting-edge technologies shaping our future.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-flask text-success fs-1 mb-3"></i>
                                <h5 class="fw-bold">Scientific Discoveries</h5>
                                <p>The latest breakthroughs in research that are expanding our understanding of the world.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-laptop-code text-info fs-1 mb-3"></i>
                                <h5 class="fw-bold">Tech Industry News</h5>
                                <p>Updates on major tech companies, startups, and industry trends that matter to you.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="mb-4 fw-bold text-center">Our Team</h2>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <p class="fs-5 mb-4 text-center">Our team consists of experienced tech enthusiasts, journalists, and industry experts who are passionate about delivering valuable content. We work tirelessly to research, verify, and present information that matters to you.</p>
                        
                        <div class="row mt-4 justify-content-center">
                            <div class="col-md-4 text-center mb-4">
                                <div class="avatar mx-auto mb-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                            <img src="{{ asset('assets/images/Creater.jpg') }}" alt="Moatamen Naief" class="rounded-circle w-100 h-100 object-fit-cover">
                                        {{-- @else
                                            <i class="fas fa-code text-primary fs-1"></i>
                                        @endif --}}
                                    </div>
                                </div>
                                <h5 class="fw-bold">Moatamen Naief</h5>
                                <p class="text-muted">CTO</p>
                            </div>

                            <div class="col-md-4 text-center mb-4">
                                <div class="avatar mx-auto mb-3">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                        <i class="fas fa-laptop text-info fs-1"></i>
                                    </div>
                                </div>
                                <h5 class="fw-bold"> HENNI karim Abdelkader </h5>
                                <p class="text-muted">Tech Analyst</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="mb-4 fw-bold">Our Story</h2>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="fs-5">Founded in 2025, Tech Expo Newsletter began with a simple vision: to create a platform where technology and science become accessible and exciting for everyone. What started as a small project has grown into a trusted source of information for thousands of readers worldwide.</p>
                        <p class="fs-5">We believe in building lasting relationships with our readers through honesty, reliability, and valuable insights. Our commitment to quality journalism and accessible expertise continues to drive our growth and evolution.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
    <x-subscribe-footer-card />
@endsection