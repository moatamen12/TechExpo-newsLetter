@extends('layouts.app')
@section('title', $newsletter->title)

@php
    $backBtn = [
        'link' => route('dashboard.newsletter'),
        'text' => '<i class="fas fa-arrow-left me-2"></i>Back to Newsletters'
    ];
    
    $editBtn = [];
    $deleteBtn = [];
    
    // Only show edit/delete buttons for drafts and scheduled newsletters
    if(in_array($newsletter->status, ['draft', 'scheduled'])) {
        $editBtn = [
            'link' => route('newsletter.edit', $newsletter->id),
            'text' => '<i class="fas fa-edit me-2"></i>Edit Newsletter'
        ];
        
        $deleteBtn = [
            'link' => '#',
            'text' => '<i class="fas fa-trash me-2"></i>Delete Newsletter',
            'class' => 'btn-danger',
            'onclick' => 'confirmDelete(' . $newsletter->id . ')'
        ];
    }
    
    $buttons = array_filter([$backBtn, $editBtn, $deleteBtn]);
@endphp

@section('content')
<div class="container px-5">
    <x-dashboard-header 
        title="Newsletter Preview" 
        description="Preview how your newsletter will appear to subscribers"
        :btn="$buttons"
        class="mb-4">
    </x-dashboard-header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Newsletter Status Info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $newsletter->title }}</h5>
                            <p class="text-muted mb-2">{{ $newsletter->summary }}</p>
                            <small class="text-muted">
                                Created: {{ $newsletter->created_at->format('M j, Y g:i A') }}
                                @if($newsletter->sent_at)
                                    • Sent: {{ $newsletter->sent_at->format('M j, Y g:i A') }}
                                @endif
                                @if($newsletter->scheduled_at)
                                    • Scheduled: {{ $newsletter->scheduled_at->format('M j, Y g:i A') }}
                                @endif
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge fs-6 
                                @if($newsletter->status === 'sent') bg-success
                                @elseif($newsletter->status === 'scheduled') bg-warning text-dark
                                @elseif($newsletter->status === 'draft') bg-secondary
                                @else bg-danger
                                @endif
                            ">
                                {{ ucfirst($newsletter->status) }}
                            </span>
                            
                            @if($newsletter->status === 'sent')
                                <div class="mt-2">
                                    <small class="text-muted d-block">Sent to {{ $newsletter->total_sent ?? 0 }} recipients</small>
                                    @if($newsletter->total_failed > 0)
                                        <small class="text-danger d-block">{{ $newsletter->total_failed }} failed</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons for Draft/Scheduled -->
    @if($newsletter->status === 'draft')
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body text-center">
                        <h6 class="mb-3">This newsletter is saved as draft</h6>
                        <a href="{{ route('newsletter.send-options', $newsletter->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-paper-plane me-2"></i>Send Newsletter
                        </a>
                        <a href="{{ route('newsletter.test-send', $newsletter->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-flask me-2"></i>Send Test Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($newsletter->status === 'scheduled')
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                    <div class="card-body text-center">
                        <h6 class="mb-3">This newsletter is scheduled to be sent on {{ $newsletter->scheduled_at->format('M j, Y \a\t g:i A') }}</h6>
                        <a href="{{ route('newsletter.send', $newsletter->id) }}" class="btn btn-primary me-2" 
                           onclick="return confirm('Are you sure you want to send this newsletter now instead of waiting for the scheduled time?')">
                            <i class="fas fa-paper-plane me-2"></i>Send Now
                        </a>
                        <a href="{{ route('newsletter.test-send', $newsletter->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-flask me-2"></i>Send Test Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Newsletter Preview -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Newsletter Preview
                    </h5>
                    <small class="text-muted">This is how your newsletter will appear in subscribers' emails</small>
                </div>
                <div class="card-body p-0">
                    <!-- Include the newsletter email template -->
                    <div class="newsletter-preview" style="max-height: 800px; overflow-y: auto;">
                        @include('mail.newsletter-email', [
                            'newsletter' => $newsletter,
                            'author' => $newsletter->author->user ?? auth()->user()
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(in_array($newsletter->status, ['draft', 'scheduled']))
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Newsletter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this newsletter?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Newsletter</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function confirmDelete(newsletterId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `{{ route('newsletter.destroy', '') }}/${newsletterId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush

@push('styles')
<style>
    .newsletter-preview {
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .newsletter-preview .container {
        margin: 0 auto;
        box-shadow: none;
    }
    
    /* Override some email styles for better preview */
    .newsletter-preview body {
        background-color: transparent !important;
    }
</style>
@endpush