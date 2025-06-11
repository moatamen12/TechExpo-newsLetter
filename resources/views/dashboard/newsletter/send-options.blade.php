@extends('layouts.app')
@section('title', 'Newsletter Send Options')

@php
    $btn = [
        'link' => route('newsletter.edit', $newsletter->id),
        'text' => '<i class="fas fa-edit me-2"></i>Edit Newsletter'
    ];
@endphp

@section('content')
<div class="container px-5">
    <x-dashboard-header 
        title="Send Newsletter" 
        description="Choose who to send your newsletter to"
        :btn="[$btn]"
        class="mb-2">
    </x-dashboard-header>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Newsletter Preview -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Newsletter Preview</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $newsletter->title }}</h6>
                    <p class="card-text text-muted">{{ $newsletter->summary }}</p>
                    @if($newsletter->featured_image)
                        <img src="{{ asset('storage/' . $newsletter->featured_image) }}" 
                             class="img-fluid rounded mb-3" alt="Featured Image">
                    @endif
                    <div class="content-preview" style="max-height: 300px; overflow-y: auto;">
                        {!! Str::limit($newsletter->content, 500) !!}
                    </div>
                    <small class="text-muted">
                        Created: {{ $newsletter->created_at->format('M j, Y g:i A') }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Send Options -->
        <div class="col-md-6">
            <form action="{{ route('newsletter.send.confirm', $newsletter->id) }}" method="POST">
                @csrf
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Send Options</h5>
                    </div>
                    <div class="card-body">
                        <!-- Recipient Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Who should receive this newsletter?</label>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="all-subscribers" 
                                    value="all" {{ old('recipient_type', 'all') == 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="all-subscribers">
                                    <strong>All Subscribers</strong><br>
                                    <small class="text-muted">Send to all {{ $allSubscribers }} subscribers</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="category-subscribers" 
                                    value="category" {{ old('recipient_type') == 'category' ? 'checked' : '' }}>
                                <label class="form-check-label" for="category-subscribers">
                                    <strong>My Subscribers Only</strong><br>
                                    <small class="text-muted">Send to {{ $categorySubscribers }} subscribers following you</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="test-send" 
                                    value="test" {{ old('recipient_type') == 'test' ? 'checked' : '' }}>
                                <label class="form-check-label" for="test-send">
                                    <strong>Test Send</strong><br>
                                    <small class="text-muted">Send only to yourself for testing</small>
                                </label>
                            </div>
                        </div>

                        <!-- Send Time Options -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">When to send?</label>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="send_time" id="send-immediate" 
                                    value="immediate" {{ old('send_time', 'immediate') == 'immediate' ? 'checked' : '' }}>
                                <label class="form-check-label" for="send-immediate">
                                    <strong>Send Immediately</strong><br>
                                    <small class="text-muted">Send right now</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="send_time" id="send-scheduled-option" 
                                    value="scheduled" {{ old('send_time') == 'scheduled' ? 'checked' : '' }}>
                                <label class="form-check-label" for="send-scheduled-option">
                                    <strong>Schedule for Later</strong><br>
                                    <small class="text-muted">Choose a specific date and time</small>
                                </label>
                            </div>
                        </div>

                        <!-- Schedule Date Time -->
                        <div class="mb-4" id="schedule-datetime-section" style="display: none;">
                            <label for="scheduled_at_confirm" class="form-label fw-bold">Schedule Date & Time</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at_confirm" name="scheduled_at" 
                                value="{{ old('scheduled_at') }}" min="{{ now()->format('Y-m-d\TH:i') }}">
                            <small class="text-muted">Select when you want to send this newsletter</small>
                        </div>

                        <!-- Confirmation -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Ready to send?</strong><br>
                            Please review your selections above before proceeding.
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard.newsletter') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="send-confirm-btn">
                                <i class="fas fa-paper-plane me-2"></i><span id="send-btn-text">Send Newsletter</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sendImmediate = document.getElementById('send-immediate');
        const sendScheduled = document.getElementById('send-scheduled-option');
        const scheduleDatetimeSection = document.getElementById('schedule-datetime-section');
        const sendBtnText = document.getElementById('send-btn-text');
        
        function toggleScheduleSection() {
            if (sendScheduled.checked) {
                scheduleDatetimeSection.style.display = 'block';
                sendBtnText.textContent = 'Schedule Newsletter';
            } else {
                scheduleDatetimeSection.style.display = 'none';
                sendBtnText.textContent = 'Send Newsletter';
            }
        }
        
        sendImmediate.addEventListener('change', toggleScheduleSection);
        sendScheduled.addEventListener('change', toggleScheduleSection);
        
        // Initialize on page load
        toggleScheduleSection();
    });
</script>
@endpush