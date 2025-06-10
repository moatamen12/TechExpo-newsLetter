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

    // Monthly Performance Chart
    createPerformanceChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Articles Published',
                    data: data.articles || [12, 19, 15, 25, 22, 30],
                    borderColor: this.colors.primary,
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Total Views',
                    data: data.views || [150, 230, 180, 320, 280, 380],
                    borderColor: this.colors.success,
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
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

    // Audience Growth Chart
    createAudienceChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        return new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.labels || ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'New Followers',
                    data: data.followers || [45, 52, 38, 67],
                    backgroundColor: this.colors.info
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

    // Monthly Performance Chart (Stats Page Version)
    createMonthlyChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.months || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Views',
                    data: data.views || [150, 230, 180, 320, 280, 380],
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
                            color: this.colors.light
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

    // Audience Growth Chart (Stats Page Version)
    createAudienceGrowthChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        return new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.months || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Users',
                    data: data.users || [100, 150, 200, 250, 300, 350],
                    borderColor: this.colors.info,
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
                            color: this.colors.light
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

    // Category Doughnut Chart
    createCategoryChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const categoryData = data || [];
        
        return new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryData.map(cat => cat.name),
                datasets: [{
                    data: categoryData.map(cat => cat.count || cat.percentage),
                    backgroundColor: this.chartColors.slice(0, categoryData.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
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

    // Initialize all charts for dashboard home page
    initDashboardCharts(data) {
        this.createPerformanceChart('performanceChart', data.monthlyData || {});
        this.createAudienceChart('audienceChart', data.audienceData || {});
        this.createCategoryChart('categoryChart', data.categoryStats || []);
    }

    // Initialize all charts for stats page
    initStatsCharts(data) {
        this.createMonthlyChart('monthlyChart', data.monthlyData || {});
        this.createAudienceGrowthChart('audienceChart', data.audienceGrowth || {});
        this.createCategoryChart('categoryChart', data.categoryStats || []);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.dashboardCharts = new DashboardCharts();
});