class DashboardCharts {
    constructor() {
        this.colors = {
            primary: '#007bff',
            success: '#28a745',
            info: '#17a2b8',
            warning: '#ffc107',
            danger: '#dc3545',
            secondary: '#6c757d',
            light: '#f8f9fa',
            dark: '#343a40'
        };
        
        this.chartColors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'];
    }

    // Check if data has meaningful values (not just zeros)
    hasRealData(data) {
        if (!data || !Array.isArray(data)) return false;
        return data.some(val => val > 0);
    }

    // Monthly Performance Chart - only show with real data
    createPerformanceChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        // Check for real data - articles published or views > 0
        const hasArticleData = this.hasRealData(data.articles);
        const hasViewData = this.hasRealData(data.views);

        if (!hasArticleData && !hasViewData) {
            this.showEmptyState(ctx, 'Start publishing articles to see performance data');
            return;
        }

        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.months || [],
                datasets: [{
                    label: 'Articles Published',
                    data: data.articles || [],
                    borderColor: this.colors.primary,
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    pointBackgroundColor: this.colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }, {
                    label: 'Total Views',
                    data: data.views || [],
                    borderColor: this.colors.success,
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    pointBackgroundColor: this.colors.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: this.colors.light
                        },
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Audience Growth Chart - only show with real followers
    createAudienceChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const hasFollowerData = this.hasRealData(data.followers);
        
        if (!hasFollowerData) {
            this.showEmptyState(ctx, 'Start building your audience to see growth data');
            return;
        }

        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.months || [],
                datasets: [{
                    label: 'Followers',
                    data: data.followers || [],
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderColor: this.colors.info,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: this.colors.info,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Followers: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
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
    }

    // Monthly Chart for stats page
    createMonthlyChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const hasViewData = this.hasRealData(data.views);

        if (!hasViewData) {
            this.showEmptyState(ctx, 'Publish articles to see monthly performance');
            return;
        }

        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.months || [],
                datasets: [{
                    label: 'Views',
                    data: data.views || [],
                    borderColor: '#20c997',
                    backgroundColor: 'rgba(32, 201, 151, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#20c997',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
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
                            color: this.colors.light
                        },
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
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
    }

    // Category Chart - only show with real categories
    createCategoryChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const hasRealCategories = data && data.length > 0 && 
                                 data.some(cat => cat.count > 0);
        
        if (!hasRealCategories) {
            this.showEmptyState(ctx, 'Publish articles in different categories to see distribution');
            return;
        }
        
        return new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: data.map(cat => cat.name),
                datasets: [{
                    data: data.map(cat => cat.count),
                    backgroundColor: this.chartColors.slice(0, data.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dataset = context.dataset;
                                const total = dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((dataset.data[context.dataIndex] / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} articles (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                elements: {
                    arc: {
                        borderWidth: 0
                    }
                }
            }
        });
    }

    // Enhanced empty state
    showEmptyState(canvas, message) {
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Set styles
        ctx.fillStyle = '#6c757d';
        ctx.font = '14px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        
        // Draw chart icon
        ctx.strokeStyle = '#dee2e6';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(centerX - 30, centerY - 10);
        ctx.lineTo(centerX - 10, centerY - 25);
        ctx.lineTo(centerX + 10, centerY - 5);
        ctx.lineTo(centerX + 30, centerY - 20);
        ctx.stroke();
        
        // Draw axes
        ctx.beginPath();
        ctx.moveTo(centerX - 40, centerY + 20);
        ctx.lineTo(centerX + 40, centerY + 20);
        ctx.moveTo(centerX - 40, centerY + 20);
        ctx.lineTo(centerX - 40, centerY - 40);
        ctx.stroke();
        
        // Draw message
        ctx.fillText(message, centerX, centerY + 40);
    }

    // Initialize dashboard charts with data validation
    initDashboardCharts(data) {
        console.log('Dashboard charts data:', data); // Debug log
        this.createPerformanceChart('performanceChart', data.monthlyData || {});
        this.createAudienceChart('audienceChart', data.audienceGrowth || {});
        this.createCategoryChart('categoryChart', data.categoryStats || []);
    }

    // Initialize stats charts with data validation  
    initStatsCharts(data) {
        console.log('Stats charts data:', data); // Debug log
        this.createMonthlyChart('monthlyChart', data.monthlyData || {});
        this.createAudienceChart('audienceChart', data.audienceGrowth || {});
        this.createCategoryChart('categoryChart', data.categoryStats || []);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.dashboardCharts = new DashboardCharts();
});