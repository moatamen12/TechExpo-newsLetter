@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <!-- Profile Header Section -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <!-- Avatar -->
                            <div class="col-auto">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" 
                                     style="width: 80px; height: 80px; font-size: 24px; font-weight: 600;">
                                    MN
                                </div>
                            </div>
                            
                            <!-- Profile Info -->
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h2 class="mb-1" style="font-weight: 600;">Moatamen Naief</h2>
                                        <p class="text-muted mb-3">Tech enthusiast and software developer</p>
                                        
                                        <!-- Tags -->
                                        <div class="mb-3">
                                            <span class="badge me-2" style="background-color: var(--btn-color); color: white; padding: 6px 12px;">React</span>
                                            <span class="badge me-2" style="background-color: var(--btn-color); color: white; padding: 6px 12px;">JavaScript</span>
                                            <span class="badge me-2" style="background-color: var(--btn-color); color: white; padding: 6px 12px;">Web Development</span>
                                            <span class="badge" style="background-color: var(--btn-color); color: white; padding: 6px 12px;">AI</span>
                                        </div>
                                        
                                        <!-- Stats -->
                                        <div class="row">
                                            <div class="col-auto">
                                                <div class="text-center">
                                                    <h4 class="mb-0" style="font-weight: 700;">24</h4>
                                                    <small class="text-muted">Articles Read</small>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="text-center">
                                                    <h4 class="mb-0" style="font-weight: 700;">3</h4>
                                                    <small class="text-muted">Writers Following</small>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="text-center">
                                                    <h4 class="mb-0" style="font-weight: 700;">12</h4>
                                                    <small class="text-muted">Reactions</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="text-end">
                                        <button class="btn btn-subscribe mb-2 d-block" style="width: 180px; padding: 12px 20px;">Edit Profile</button>
                                        <button class="btn secondary-btn d-block" style="width: 180px; padding: 12px 20px;">Account Settings</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <div class="row mt-4 ">
            <div class="col-12 ">
                <ul class="nav nav-pills border-bottom " id="profileTab" role="tablist" style="border-color: #e9ecef !important;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active border-0 bg-transparent" id="reading-history-tab" 
                                data-bs-toggle="pill" data-bs-target="#reading-history" type="button" 
                                role="tab" aria-controls="reading-history" aria-selected="true"
                                style="color: var(--primary-text); font-weight: 500;">
                            Reading History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-0 bg-transparent" id="reactions-tab" 
                                data-bs-toggle="pill" data-bs-target="#reactions" type="button" 
                                role="tab" aria-controls="reactions" aria-selected="false"
                                style="color: var(--tab-txt); font-weight: 500;">
                            Your Reactions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-0 bg-transparent" id="subscriptions-tab" 
                                data-bs-toggle="pill" data-bs-target="#subscriptions" type="button" 
                                role="tab" aria-controls="subscriptions" aria-selected="false"
                                style="color: var(--tab-txt); font-weight: 500;">
                            Subscriptions
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="tab-content my-4 card p-3 bg-whit" id="profileTabContent">
            <!-- Reading History Tab -->
            <div class="tab-pane fade show active" id="reading-history" role="tabpanel" 
                 aria-labelledby="reading-history-tab">
                <h4 class="mb-4" style="font-weight: 600;">Recently Read Articles</h4>
                
                <div class="row">
                    <!-- Article 1 -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-1 border-light-subtle articall-card-hover h-100">
                            <div class="article-image-container">
                                <div class="d-flex align-items-center justify-content-center h-100" 
                                     style="background-color: #e8f5f5; color: var(--btn-color); font-size: 2rem;">
                                    &lt;/&gt;
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge" style="background-color: var(--btn-color); color: white; font-size: 0.75rem;">Web Development</span>
                                    <small class="text-muted ms-2">April 20, 2025</small>
                                </div>
                                <h5 class="card-title mb-3" style="font-weight: 600; line-height: 1.4;">
                                    The Future of Web Development: AI-Driven Interfaces
                                </h5>
                                <p class="text-muted mb-3" style="font-size: 0.9rem;">
                                    Discover how artificial intelligence is transforming the landscape of web development, enabling more intuitive user...
                                </p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px; font-size: 0.75rem;">S</div>
                                        <small class="text-muted">Sarah Johnson</small>
                                    </div>
                                    <small class="text-muted">12 min read</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article 2 -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm articall-card-hover h-100">
                            <div class="article-image-container">
                                <div class="d-flex align-items-center justify-content-center h-100" 
                                     style="background-color: #e8f5f5; color: var(--btn-color); font-size: 2rem;">
                                    &lt;/&gt;
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge" style="background-color: var(--btn-color); color: white; font-size: 0.75rem;">Web Development</span>
                                    <small class="text-muted ms-2">April 18, 2025</small>
                                </div>
                                <h5 class="card-title mb-3" style="font-weight: 600; line-height: 1.4;">
                                    Understanding the New React Server Components
                                </h5>
                                <p class="text-muted mb-3" style="font-size: 0.9rem;">
                                    React Server Components represent a paradigm shift in how we build React applications. This article explores the...
                                </p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px; font-size: 0.75rem;">A</div>
                                        <small class="text-muted">Alex Chen</small>
                                    </div>
                                    <small class="text-muted">8 min read</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article 3 -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm articall-card-hover h-100">
                            <div class="article-image-container">
                                <div class="d-flex align-items-center justify-content-center h-100" 
                                     style="background-color: #e8f5f5; color: var(--btn-color); font-size: 2rem;">
                                    &lt;/&gt;
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge" style="background-color: var(--btn-color); color: white; font-size: 0.75rem;">AI & Machine Learning</span>
                                    <small class="text-muted ms-2">April 15, 2025</small>
                                </div>
                                <h5 class="card-title mb-3" style="font-weight: 600; line-height: 1.4;">
                                    Machine Learning Models for Predictive Analytics
                                </h5>
                                <p class="text-muted mb-3" style="font-size: 0.9rem;">
                                    Learn about the most effective machine learning models for implementing predictive analytics in your business applications.
                                </p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px; font-size: 0.75rem;">M</div>
                                        <small class="text-muted">Michael Rodriguez</small>
                                    </div>
                                    <small class="text-muted">10 min read</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Your Reactions Tab -->
            <div class="tab-pane fade" id="reactions" role="tabpanel" aria-labelledby="reactions-tab">
                <h4 class="mb-4" style="font-weight: 600;">Your Reactions</h4>
                <div class="text-center py-5">
                    <p class="text-muted">No reactions yet. Start engaging with articles to see your activity here.</p>
                </div>
            </div>
            
            <!-- Subscriptions Tab -->
            <div class="tab-pane fade" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
                <h4 class="mb-4" style="font-weight: 600;">Your Subscriptions</h4>
                <div class="text-center py-5">
                    <p class="text-muted">No subscriptions yet. Follow writers and topics to see them here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-pills .nav-link.active {
    background-color: transparent !important;
    border-bottom: 2px solid var(--btn-color) !important;
    color: var(--primary-text) !important;
    border-radius: 0 !important;
}

.nav-pills .nav-link {
    border-radius: 0 !important;
    padding: 1rem 0 !important;
    margin-right: 2rem !important;
}

.nav-pills .nav-link:hover {
    background-color: transparent !important;
    color: var(--btn-color) !important;
}

.article-image-container {
    height: 200px;
    overflow: hidden;
    border-radius: 0.5rem 0.5rem 0 0;
}
</style>
@endsection