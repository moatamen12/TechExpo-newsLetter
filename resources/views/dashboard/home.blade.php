@extends('layouts.app')
@section('title', 'Dashboard')
@php
    $CreateBTN = [
        'link' => 'dashboard/articles/create',
        'text' => '<span><i class="fa-regular fa-pen-to-square" style="color: #ffffff;"></i> New Content</span>'
    ];
@endphp

@push('styles')
    <link href="{{ asset('assets/css/state_style.css') }}" rel="stylesheet">
@endpush

@section('content')
    <section class="p-5">
        <div class="container-fluid">
            {{-- the header of the page --}}
            <div>
                <x-dashboard-header 
                    title="{{ $userData['userName'] }} Dashboard" 
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

            {{-- Update the dashboard cards section --}}
            <div class="d-flex flex-wrap justify-content-between align-items-stretch gap-2">
                {{-- Dashboard Cards --}}
                <x-dashboard-card 
                    title="Followers" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-1"/>
                        <circle cx="16" cy="7" r="3"/>
                    </svg>'
                    :count="$userData['followersCount'] ?? 0"
                    :percentageChange="$userData['followersPercentage']"
                    :changeDirection="$userData['followersDirection']">
                </x-dashboard-card>

                <x-dashboard-card 
                    title="Total Content" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                            <path d="M10 9H8"/>
                            <path d="M16 13H8"/>
                            <path d="M16 17H8"/>
                        </svg>'
                    :count="$userData['totalArticles']"
                    :percentageChange="$userData['articlesPercentage']"
                    :changeDirection="$userData['articlesDirection']">
                </x-dashboard-card>

                <x-dashboard-card 
                    title="Total Views" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>'   
                    :count="$userData['totalViews']"
                    :percentageChange="$userData['viewsPercentage']"
                    :changeDirection="$userData['viewsDirection']">
                </x-dashboard-card>

                <x-dashboard-card 
                    title="Reactions" 
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512" fill="#0b0b0b" stroke="#0b0b0b" stroke-width="1">
                        <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 112.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-68.9 5.3-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/>
        </svg>'   
        :count="$userData['totalLikes'] + $userData['totalComments']"
        :percentageChange="$userData['reactionsPercentage']"
        :changeDirection="$userData['reactionsDirection']">
    </x-dashboard-card>
            </div>

            {{-- Analytics Dashboard Section (Exact copy from stats.blade.php) --}}
            <!-- Header -->
            {{-- <div class="row mb-4 mt-4">
                <div class="col-12">
                    <h2 class="fw-bold mb-1">Analytics Dashboard</h2>
                    <p class="text-muted">Track your content performance and audience growth</p>
                </div>
            </div> --}}

            <!-- Charts Row -->
            <div class="row my-4">
                <!-- Monthly Performance Chart -->
                <div class="col-lg-6 mb-4 ">
                    <div class="card h-100 border-light shadow-sm">
                        <div class="card-header bg-white border-0 pb-0 mb-3">
                            <h5 class="card-title fw-bold mb-0">Monthly Performance</h5>
                            <p class="text-muted small mb-0">Track your article views over the last 6 months to see your content's reach and engagement trends.</p>
                        </div>
            
                        <div class="card-body">
                            <canvas id="monthlyChart" width="400" height="200"></canvas>
                        </div>
                    </div>         
                </div>

                <!-- Audience Growth Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-light shadow-sm">
                        <div class="card-header bg-white border-0 pb-0 mb-3">
                            <h5 class="card-title fw-bold mb-0">Audience Growth</h5>
                            <p class="text-muted small mb-0">Monitor your follower growth and see how your audience is expanding month by month.</p>
                        </div>
                        <div class="card-body">
                            <canvas id="audienceChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Analysis Row -->
            <div class="row">
                <!-- Top Performing Articles -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100 border-light shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h5 class="card-title fw-bold mb-3">Top Performing Articles</h5>
                            <p class="text-muted small mb-0">Your most successful articles ranked by total engagement (views, likes, and comments combined).</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        @forelse($topArticles as $article)
                                        <tr class="border-bottom">
                                            <td class="py-3">
                                                <div>
                                                    <a href="{{route('articles.show',$article['article_id'])}}"
                                                       class="text-reset btn-link stretched-link fw-bold position-relative">
                                                        <strong class="d-block text-dark">{{ $article['title'] }}</strong>
                                                    </a>
                                                    <small class="text-muted">{{ $article['date'] }}</small>
                                                </div>
                                            </td>
                                            <td class="py-3 text-end">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="badge bg-light text-dark me-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        {{ number_format($article['views']) }}
                                                    </span>
                                                    <span class="badge bg-light text-dark me-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                                        </svg>
                                                        {{ $article['likes'] }}
                                                    </span>
                                                    <span class="badge bg-light text-dark">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                        </svg>
                                                        {{ $article['comments'] }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">
                                                No articles found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Recent Activity and Categories -->
                <div class="col-lg-4">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pb-2">
                            <h6 class="card-title fw-bold mb-3">Recent Activity</h6>
                        </div>
                        <div class="card-body py-3">
                            @forelse($recentActivity as $activity)
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-{{ $activity['type'] == 'article' ? 'primary' : ($activity['type'] == 'comment' ? 'success' : ($activity['type'] == 'like' ? 'danger' : ($activity['type'] == 'save' ? 'warning' : ($activity['type'] == 'follower' ? 'info' : ($activity['type'] == 'newsletter' ? 'dark' : 'secondary'))))) }} rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        @if($activity['type'] == 'article')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14,2 14,8 20,8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10,9 9,9 8,9"></polyline>
                                            </svg>
                                        @elseif($activity['type'] == 'newsletter')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                        @elseif($activity['type'] == 'comment')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        @elseif($activity['type'] == 'like')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                            </svg>
                                        @elseif($activity['type'] == 'save')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        @elseif($activity['type'] == 'follower')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M22 21v-2a4 4 0 0 0-3-1"/>
                                                <circle cx="16" cy="7" r="3"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small">
                                        <strong>{{ $activity['title'] }}</strong>
                                        @if($activity['type'] == 'newsletter' && isset($activity['status']))
                                            <span class="badge badge-sm ms-1 
                                                @if($activity['status'] == 'sent') bg-success 
                                                @elseif($activity['status'] == 'scheduled') bg-warning text-dark
                                                @else bg-secondary 
                                                @endif">
                                                {{ ucfirst($activity['status']) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">{{ $activity['description'] }}</div>
                                    <div class="small text-muted">{{ $activity['time'] }}</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-3">
                                <small class="text-muted">No recent activity</small>
                            </div>
                            @endforelse
                        </div>

                        <!-- Content Categories Pie Chart (Smaller) -->
                        <div class="card border-light shadow-sm">
                            <div class="card-header bg-white border-0 pb-2">
                                <h6 class="card-title fw-bold mb-3">Content Categories</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <canvas id="categoryChart" width="150" height="150" style="max-height: 150px;"></canvas>
                                </div>
                                <div>
                                    @foreach($categoryStats as $category)
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2" style="width: 8px; height: 8px; background-color: {{ $loop->index == 0 ? '#20c997' : ($loop->index == 1 ? '#17a2b8' : ($loop->index == 2 ? '#6f42c1' : '#fd7e14')) }};"></div>
                                            <span class="small text-muted">{{ $category['name'] }}</span>
                                        </div>
                                        <span class="small fw-bold">{{ $category['percentage'] }}%</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/dashboard-charts.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize stats charts (same as stats page)
            if (window.dashboardCharts) {
                window.dashboardCharts.initStatsCharts({
                    monthlyData: @json($monthlyData ?? []),
                    audienceGrowth: @json($audienceGrowth ?? []),
                    categoryStats: @json($categoryStats ?? [])
                });
            }
        });
    </script>
@endpush