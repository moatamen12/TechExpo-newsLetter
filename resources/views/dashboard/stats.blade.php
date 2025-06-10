@extends('layouts.app')

@section('title', 'Analytics & Stats')
@push('styles')
    <link href="{{ asset('assets/css/stats-dashboard.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1">Analytics Dashboard</h2>
            <p class="text-muted">Track your content performance and audience growth</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Performance Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title fw-bold mb-0">Monthly Performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Audience Growth Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title fw-bold mb-0">Audience Growth</h5>
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
                    <h5 class="card-title fw-bold mb-0">Top Performing Articles</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                @forelse($topArticles as $article)
                                <tr class="border-bottom">
                                    <td class="py-3">
                                        <div>
                                            <strong class="d-block text-dark">{{ $article['title'] }}</strong>
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

        <!-- Content Categories Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-bold mb-0">Content Categories</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                        <canvas id="categoryChart" width="200" height="200"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($categoryStats as $category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: {{ $loop->index == 0 ? '#20c997' : ($loop->index == 1 ? '#17a2b8' : ($loop->index == 2 ? '#6f42c1' : '#fd7e14')) }};"></div>
                                <small class="text-muted">{{ $category['name'] }}</small>
                            </div>
                            <small class="fw-bold">{{ $category['percentage'] }}%</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['months']),
            datasets: [{
                label: 'Views',
                data: @json($monthlyData['views']),
                borderColor: '#20c997',
                backgroundColor: 'rgba(32, 201, 151, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f8f9fa'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Audience Growth Chart
    const audienceCtx = document.getElementById('audienceChart').getContext('2d');
    new Chart(audienceCtx, {
        type: 'line',
        data: {
            labels: @json($audienceGrowth['months']),
            datasets: [{
                label: 'Users',
                data: @json($audienceGrowth['users']),
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f8f9fa'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Category Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($categoryStats);
    const colors = ['#20c997', '#17a2b8', '#6f42c1', '#fd7e14', '#ffc107'];
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                data: categoryData.map(item => item.percentage),
                backgroundColor: colors.slice(0, categoryData.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '60%'
        }
    });
});
</script>
@endpush
@endsection