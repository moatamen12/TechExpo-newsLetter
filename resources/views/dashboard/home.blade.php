@extends('layouts.app')
@section('title', 'Dashboard')
@php
    $CreateBTN = [
        'link' => 'dashboard/articles/create',
        'text' => '<span><i class="fa-regular fa-pen-to-square" style="color: #ffffff;"></i> New Content</span>'
    ];
    // Define the tabs structure for Articles, Newsletters, Analytics 
    $dashboardTabs = [
        'articles' => [
            'activeTab' => 'articles',
            'id' => 'articles-tab',
            'ariaControls' => 'articlesContent',
            'txt' => 'Articles'
        ],
        'newsletters' => [
            'activeTab' => 'newsletters',
            'id' => 'newsletters-tab',
            'ariaControls' => 'newslettersContent',
            'txt' => 'Newsletters'
        ]
    ];
    
    // Set which tab is active (you can set this based on a route parameter or other logic)
    $activeTab = 'articles'; // Default active tab
    $newsletters = $articles; 
@endphp
@section('content')
    <section class="p-5">
        <div class="container-fluid">
            {{-- the header of the page --}}
            <div>
                <x-dashboard-header 
                    title="{{$user['userName']}} Dashboard" 
                    description="Manage your articles, newsletters, and audience" 
                    :btn="[$CreateBTN]">
                </x-dashboard-header>   
                {{-- Add this section to display session messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif             
            </div>


            <div class="d-flex flex-wrap justify-content-between align-items-stretch gap-2">
                {{-- Total Subscribers --}}
                <x-dashboard-card 
                    title="Followers" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-1"/>
                        <circle cx="16" cy="7" r="3"/>
                    </svg>'
                    :count="$user['totalArticles']">
                </x-dashboard-card>

                {{-- Articles  --}}
                <x-dashboard-card 
                    title="Total Content " 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                            <path d="M10 9H8"/>
                            <path d="M16 13H8"/>
                            <path d="M16 17H8"/>
                        </svg>'
  
                    :count="$user['totalArticles']">
                </x-dashboard-card>

                {{-- Total Views --}}
                <x-dashboard-card 
                    title="Total Views" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>'   
                    :count="$user['totalViews']">
                </x-dashboard-card>

                {{-- Total Likes --}}
                <x-dashboard-card 
                    title="Reactions" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512" fill="#0b0b0b" stroke="#0b0b0b" stroke-width="1">
                        <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/>
                    </svg>'   
                    :count="$user['totalLikes'] + $user['totalComments']">
                </x-dashboard-card>
            </div>

            <x-dashboard-header 
                title="Your Top Performing Content" 
                description="" 
                class="mt-3">
            </x-dashboard-header>

            <div class="d-flex justify-content-between align-items-center mb-3">
                {{-- for the tabs "articles","newsletters"--}}
                <x-taps :taps="$dashboardTabs" :activeTab="$activeTab" />
                <x-seeMore 
                    link="dashboard.articles" 
                    text="See all youe Content" 
                />
            </div>


            {{-- Add the tab content panes --}}
            <div class="tab-content" id="pills-tabContent">

                <div class="tab-pane fade show active" id="articlesContent" role="tabpanel" aria-labelledby="articles-tab" tabindex="0">
                    <div class="card border-light p-2">
                        <h5 class="card-title fw-bold m-2">Top Performing Articles</h5>
                        <div class="card-body mt-2">
                            <x-dashboard-table :vars="$articles" tableID="articles"/>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="newslettersContent" role="tabpanel" aria-labelledby="newsletters-tab" tabindex="0">
                    <div class="card border-light p-2">
                        <h5 class="card-title fw-bold m-2">Top Performing Newsletter</h5>
                        <div class="card-body mt-2">
                            <x-dashboard-table :vars="$newsletters" tableID="newsletter"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection