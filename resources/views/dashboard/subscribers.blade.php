@extends('layouts.app')
@section('title', 'My Subscribers')

@php
    $btn = [
        'link' => route('dashboard'),
        'text' => '<i class="fas fa-arrow-left me-2"></i>Back to Dashboard'
    ];
@endphp

@section('content')
<div class="container px-5">
    <x-dashboard-header 
        title="My Subscribers" 
        description="Users who are following your content and receiving your newsletters"
        :btn="[$btn]"
        class="mb-4">
    </x-dashboard-header>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <x-dashboard-card 
                title="Total Subscribers"
                icon='<i class="fas fa-users text-primary"></i>'
                :count="$totalSubscribers"
                :percentageChange="0.0"
                changeDirection="neutral" />
        </div>
        <div class="col-md-3">
            <x-dashboard-card 
                title="New This Month"
                icon='<i class="fas fa-user-plus text-success"></i>'
                :count="$newThisMonth"
                :percentageChange="0.0"
                changeDirection="neutral" />
        </div>
        <div class="col-md-3">
            <x-dashboard-card 
                title="Active Subscribers"
                icon='<i class="fas fa-envelope-open text-info"></i>'
                :count="$activeSubscribers"
                :percentageChange="0.0"
                changeDirection="neutral" />
        </div>
        <div class="col-md-3">
            <x-dashboard-card 
                title="Engagement Rate"
                icon='<i class="fas fa-chart-line text-warning"></i>'
                :count="number_format($engagementRate, 1) . '%'"
                :percentageChange="0.0"
                changeDirection="neutral" />
        </div>
    </div>


    <!-- Subscribers List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Subscribers List
                @if($subscribers->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $subscribers->total() }}</span>
                @endif
            </h5>
            
            @if($subscribers->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i> Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportSubscribers('csv')">
                            <i class="fas fa-file-csv me-2"></i>Export as CSV
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportSubscribers('excel')">
                            <i class="fas fa-file-excel me-2"></i>Export as Excel
                        </a></li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body p-0">
            @if($subscribers->count() > 0)
                <div class="subscribers-table-container">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th scope="col">Subscriber</th>
                                <th scope="col">Email</th>
                                <th scope="col">Joined Date</th>
                                <th scope="col" style="width: 100px;">Status</th>
                                <th scope="col">Last Activity</th>
                                <th scope="col" style="width: 60px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscribers as $followerRelation)
                                @php
                                    $subscriber = $followerRelation->follower; // Get the actual user
                                    $userProfile = $subscriber->userProfile;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input subscriber-checkbox" 
                                               value="{{ $followerRelation->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($userProfile && $userProfile->profile_photo)
                                                <img src="{{ asset('storage/' . $userProfile->profile_photo) }}" 
                                                     class="rounded-circle me-3" width="40" height="40" alt="Profile">
                                            @else
                                                <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $subscriber->name }}</h6>
                                                @if($userProfile)
                                                    <small class="text-muted">{{ $userProfile->work ?? 'Reader' }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $subscriber->email }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $followerRelation->created_at->format('M j, Y') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $followerRelation->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Following
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $subscriber->updated_at ? $subscriber->updated_at->diffForHumans() : 'Never' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('subscriber.remove', $followerRelation->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to remove this follower?')"
                                                    title="Remove Follower">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $subscribers->firstItem() }} to {{ $subscribers->lastItem() }} 
                                of {{ $subscribers->total() }} subscribers
                            </small>
                        </div>
                        <div>
                            {{ $subscribers->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Subscribers Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'sort']))
                            No subscribers match your current filters. 
                            <a href="{{ route('dashboard.subscribers') }}" class="text-decoration-none">Clear filters</a>
                        @else
                            You don't have any subscribers yet. Start creating great content to attract followers!
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status', 'sort']))
                        <a href="{{ route('newsletter.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Newsletter
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions Modal -->
    <div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-labelledby="bulkActionsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionsModalLabel">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Select an action to perform on selected subscribers:</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="sendBulkNewsletter()">
                            <i class="fas fa-envelope me-2"></i>Send Newsletter
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="exportSelected()">
                            <i class="fas fa-download me-2"></i>Export Selected
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="removeSelected()">
                            <i class="fas fa-trash me-2"></i>Remove Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const subscriberCheckboxes = document.querySelectorAll('.subscriber-checkbox');
        const bulkActionsBtn = document.getElementById('bulkActionsBtn');
        
        // Select/Deselect All functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                subscriberCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleBulkActions();
            });
        }
        
        // Individual checkbox change
        subscriberCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
                const allBoxes = document.querySelectorAll('.subscriber-checkbox');
                
                // Update select all checkbox state
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = checkedBoxes.length === allBoxes.length;
                    selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length;
                }
                
                toggleBulkActions();
            });
        });
        
        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
            // You can add bulk actions button here if needed
        }
    });
    
    function exportSubscribers(format) {
        const url = `{{ route('dashboard.subscribers.export') }}?format=${format}&` + new URLSearchParams(window.location.search);
        window.open(url, '_blank');
    }
    
    function sendBulkNewsletter() {
        const selectedIds = Array.from(document.querySelectorAll('.subscriber-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one subscriber');
            return;
        }
        
        // Redirect to newsletter creation with pre-selected subscribers
        window.location.href = `{{ route('newsletter.create') }}?subscribers=${selectedIds.join(',')}`;
    }
    
    function exportSelected() {
        const selectedIds = Array.from(document.querySelectorAll('.subscriber-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one subscriber');
            return;
        }
        
        const url = `{{ route('dashboard.subscribers.export') }}?ids=${selectedIds.join(',')}&format=csv`;
        window.open(url, '_blank');
    }
    
    function removeSelected() {
        const selectedIds = Array.from(document.querySelectorAll('.subscriber-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one subscriber');
            return;
        }
        
        if (confirm(`Are you sure you want to remove ${selectedIds.length} subscribers?`)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'subscriber_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush

@push('styles')
<style>
    /* Fix dropdown positioning in table */
    .table-responsive {
        overflow: visible !important;
    }
    
    .table-responsive .dropdown-menu {
        position: fixed !important;
        z-index: 1050;
        transform: translate3d(0, 0, 0);
    }
    
    /* Alternative approach - only make horizontal scrollable */
    .subscribers-table-container {
        overflow-x: auto;
        overflow-y: visible;
    }
    
    .dropdown-menu {
        z-index: 1050;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075), 0 0.25rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    /* Ensure proper spacing for last column */
    .table td:last-child {
        position: relative;
    }
</style>
@endpush