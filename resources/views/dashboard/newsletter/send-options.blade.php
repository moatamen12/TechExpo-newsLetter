@extends('layouts.app')
@section('title', 'Newsletter Send Options')

@php
    $btn = [
        'link' => route('newsletter.edit', $newsletter->id),
        'text' => '<i class="fas fa-edit me-2"></i>Edit Newsletter'
    ];
@endphp

@section('content')
<div class="container-fluid px-4">
    <x-dashboard-header 
        title="Newsletter Options" 
        description="Choose what to do with your newsletter"
        :btn="[$btn]"
        class="mb-4">
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
        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Newsletter Preview</h5>
                </div>
                <div class="card-body">
                    <!-- Newsletter Header Info -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h4 class="card-title mb-2">{{ $newsletter->title }}</h4>
                        <p class="card-text text-muted mb-2">{{ $newsletter->summary }}</p>
                        <div class="d-flex flex-wrap gap-3 text-sm">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Created: {{ $newsletter->created_at->format('M j, Y g:i A') }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-tag me-1"></i>
                                Category: {{ $newsletter->category->name ?? 'Uncategorized' }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Status: <span class="badge badge-{{ $newsletter->status == 'draft' ? 'secondary' : ($newsletter->status == 'sent' ? 'success' : 'warning') }}">{{ ucfirst($newsletter->status) }}</span>
                            </small>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    @if($newsletter->featured_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $newsletter->featured_image) }}" 
                                 class="img-fluid rounded w-100" 
                                 alt="Featured Image"
                                 style="max-height: 300px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Newsletter Content Preview -->
                    <div class="newsletter-content-preview">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-file-alt me-2"></i>Content Preview
                        </h6>
                        <div class="content-preview border rounded p-4" 
                             style="max-height: 500px; overflow-y: auto; background-color: #fafafa;">
                            <div class="newsletter-content">
                                {!! $newsletter->content !!}
                            </div>
                        </div>
                        
                        <!-- Content Stats -->
                        <div class="mt-3 d-flex flex-wrap gap-3">
                            <small class="text-muted">
                                <i class="fas fa-align-left me-1"></i>
                                Words: {{ str_word_count(strip_tags($newsletter->content)) }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-text-height me-1"></i>
                                Characters: {{ strlen(strip_tags($newsletter->content)) }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Est. read time: {{ ceil(str_word_count(strip_tags($newsletter->content)) / 200) }} min
                            </small>
                        </div>
                    </div>

                    <!-- Quick Actions for Preview -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex gap-2">
                            <a href="{{ route('newsletter.edit', $newsletter->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit Content
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Options Panel -->
        <div class="col-lg-4 col-md-5">
            <form action="{{ route('newsletter.send.confirm', $newsletter->id) }}" method="POST">
                @csrf
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Newsletter Options</h5>
                    </div>
                    <div class="card-body">
                        <!-- Action Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">What would you like to do?</label>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="action_type" id="action-send" 
                                    value="send" {{ old('action_type', 'send') == 'send' ? 'checked' : '' }}>
                                <label class="form-check-label" for="action-send">
                                    <strong><i class="fas fa-paper-plane me-2 text-primary"></i>Send Newsletter</strong><br>
                                    <small class="text-muted">Send immediately to recipients</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="action_type" id="action-schedule" 
                                    value="schedule" {{ old('action_type') == 'schedule' ? 'checked' : '' }}>
                                <label class="form-check-label" for="action-schedule">
                                    <strong><i class="fas fa-clock me-2 text-warning"></i>Schedule Newsletter</strong><br>
                                    <small class="text-muted">Schedule for later delivery</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="action_type" id="action-draft" 
                                    value="draft" {{ old('action_type') == 'draft' ? 'checked' : '' }}>
                                <label class="form-check-label" for="action-draft">
                                    <strong><i class="fas fa-save me-2 text-secondary"></i>Save as Draft</strong><br>
                                    <small class="text-muted">Save without sending</small>
                                </label>
                            </div>
                        </div>

                        <!-- Recipient Selection (Hidden for draft) -->
                        <div class="mb-4" id="recipient-selection">
                            <label class="form-label fw-bold">Who should receive this newsletter?</label>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="all-followers" 
                                    value="all_followers" {{ old('recipient_type', 'all_followers') == 'all_followers' ? 'checked' : '' }}>
                                <label class="form-check-label" for="all-followers">
                                    <strong>All My Followers</strong><br>
                                    <small class="text-muted">Send to all {{ $allMyFollowers }} of your followers</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="selected-followers" 
                                    value="selected_followers" {{ old('recipient_type') == 'selected_followers' ? 'checked' : '' }}>
                                <label class="form-check-label" for="selected-followers">
                                    <strong>Selected Followers</strong><br>
                                    <small class="text-muted">Choose specific followers to send to</small>
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

                        <!-- Follower Selection (Hidden by default) -->
                        <div class="mb-4" id="follower-selection" style="display: none;">
                            <label class="form-label fw-bold">Select Followers</label>
                            <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="select-all-followers">
                                    <label class="form-check-label fw-bold" for="select-all-followers">
                                        Select All
                                    </label>
                                </div>
                                <hr class="my-2">
                                @php
                                    $selectedSubscribers = old('selected_followers', $newsletter->selected_subscribers ?? []);
                                @endphp
                                @foreach($myFollowers as $follower)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input follower-checkbox" type="checkbox" 
                                               name="selected_followers[]" value="{{ $follower->id }}" 
                                               id="follower-{{ $follower->id }}"
                                               {{ in_array($follower->id, $selectedSubscribers) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="follower-{{ $follower->id }}">
                                            <div class="d-flex align-items-center">
                                                @if($follower->follower->userProfile && $follower->follower->userProfile->profile_photo)
                                                    <img src="{{ asset('storage/' . $follower->follower->userProfile->profile_photo) }}" 
                                                         class="rounded-circle me-2" width="25" height="25" alt="Profile">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 25px; height: 25px;">
                                                        <i class="fas fa-user text-white" style="font-size: 10px;"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold small">{{ $follower->follower->name }}</div>
                                                    <small class="text-muted">{{ Str::limit($follower->follower->email, 20) }}</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">You can select specific followers to send this newsletter to.</small>
                        </div>

                        <!-- Schedule Date Time (Hidden by default) -->
                        <div class="mb-4" id="schedule-datetime-section" style="display: none;">
                            <label for="scheduled_at_confirm" class="form-label fw-bold">Schedule Date & Time</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at_confirm" name="scheduled_at" 
                                value="{{ old('scheduled_at') }}" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                            <small class="text-muted">Select when you want to send this newsletter (minimum 5 minutes from now)</small>
                        </div>

                        <!-- Confirmation Alert -->
                        <div class="alert alert-info" id="confirmation-alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Ready to proceed?</strong><br>
                            <span id="confirmation-text">Please review your selections above before proceeding.</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-subscribe" id="action-btn">
                                <i class="fas fa-paper-plane me-2" id="action-icon"></i>
                                <span id="action-btn-text">Send Newsletter</span>
                            </button>
                            <a href="{{ route('dashboard.newsletter') }}" class="btn secondary-btn">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .newsletter-content {
        font-family: Georgia, serif;
        line-height: 1.6;
    }
    
    .newsletter-content h1,
    .newsletter-content h2,
    .newsletter-content h3,
    .newsletter-content h4,
    .newsletter-content h5,
    .newsletter-content h6 {
        color: #333;
        margin-bottom: 1rem;
    }
    
    .newsletter-content p {
        margin-bottom: 1rem;
        color: #555;
    }
    
    .newsletter-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin: 1rem 0;
    }
    
    .newsletter-content ul,
    .newsletter-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    
    .newsletter-content li {
        margin-bottom: 0.5rem;
    }
    
    .newsletter-content blockquote {
        border-left: 4px solid #007bff;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
    }
    
    .content-preview {
        background: white !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .sticky-top {
        position: -webkit-sticky;
        position: sticky;
        z-index: 1020;
    }
    
    @media (max-width: 992px) {
        .sticky-top {
            position: relative;
            top: auto !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Action type elements
        const actionSend = document.getElementById('action-send');
        const actionSchedule = document.getElementById('action-schedule');
        const actionDraft = document.getElementById('action-draft');
        
        // Section elements
        const recipientSelection = document.getElementById('recipient-selection');
        const scheduleDatetimeSection = document.getElementById('schedule-datetime-section');
        const followerSelection = document.getElementById('follower-selection');
        
        // Button elements
        const actionBtn = document.getElementById('action-btn');
        const actionIcon = document.getElementById('action-icon');
        const actionBtnText = document.getElementById('action-btn-text');
        const confirmationText = document.getElementById('confirmation-text');
        
        // Recipient type elements
        const allFollowersRadio = document.getElementById('all-followers');
        const selectedFollowersRadio = document.getElementById('selected-followers');
        const testSendRadio = document.getElementById('test-send');
        const selectAllFollowers = document.getElementById('select-all-followers');
        const followerCheckboxes = document.querySelectorAll('.follower-checkbox');
        
        function updateInterface() {
            if (actionDraft.checked) {
                // Draft mode
                recipientSelection.style.display = 'none';
                scheduleDatetimeSection.style.display = 'none';
                followerSelection.style.display = 'none';
                
                actionIcon.className = 'fas fa-save me-2';
                actionBtnText.textContent = 'Save as Draft';
                actionBtn.className = 'btn secondary-btn';
                confirmationText.textContent = 'This will save your newsletter as a draft without sending it.';
                
            } else if (actionSchedule.checked) {
                // Schedule mode
                recipientSelection.style.display = 'block';
                scheduleDatetimeSection.style.display = 'block';
                
                actionIcon.className = 'fas fa-clock me-2';
                actionBtnText.textContent = 'Schedule Newsletter';
                actionBtn.className = 'btn btn-warning';
                confirmationText.textContent = 'This will schedule your newsletter for the selected date and time.';
                
                // Check follower selection
                if (selectedFollowersRadio.checked) {
                    followerSelection.style.display = 'block';
                } else {
                    followerSelection.style.display = 'none';
                }
                
            } else {
                // Send mode
                recipientSelection.style.display = 'block';
                scheduleDatetimeSection.style.display = 'none';
                
                actionIcon.className = 'fas fa-paper-plane me-2';
                actionBtnText.textContent = 'Send Newsletter';
                actionBtn.className = 'btn btn-subscribe';
                confirmationText.textContent = 'This will send your newsletter immediately to the selected recipients.';
                
                // Check follower selection
                if (selectedFollowersRadio.checked) {
                    followerSelection.style.display = 'block';
                } else {
                    followerSelection.style.display = 'none';
                }
            }
        }
        
        function toggleSelectAll() {
            const isChecked = selectAllFollowers.checked;
            followerCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        }
        
        function updateSelectAll() {
            const checkedCount = document.querySelectorAll('.follower-checkbox:checked').length;
            const totalCount = followerCheckboxes.length;
            
            if (checkedCount === 0) {
                selectAllFollowers.checked = false;
                selectAllFollowers.indeterminate = false;
            } else if (checkedCount === totalCount) {
                selectAllFollowers.checked = true;
                selectAllFollowers.indeterminate = false;
            } else {
                selectAllFollowers.checked = false;
                selectAllFollowers.indeterminate = true;
            }
        }
        
        // Event listeners
        actionSend.addEventListener('change', updateInterface);
        actionSchedule.addEventListener('change', updateInterface);
        actionDraft.addEventListener('change', updateInterface);
        allFollowersRadio.addEventListener('change', updateInterface);
        selectedFollowersRadio.addEventListener('change', updateInterface);
        testSendRadio.addEventListener('change', updateInterface);
        selectAllFollowers.addEventListener('change', toggleSelectAll);
        
        followerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAll);
        });
        
        // Initialize on page load
        updateInterface();
        updateSelectAll();
        
        // Set minimum datetime to 5 minutes from now
        const scheduledAtInput = document.getElementById('scheduled_at_confirm');
        function updateMinDatetime() {
            const now = new Date();
            now.setMinutes(now.getMinutes() + 5);
            const minDatetime = now.toISOString().slice(0, 16);
            scheduledAtInput.min = minDatetime;
        }
        
        updateMinDatetime();
        setInterval(updateMinDatetime, 60000); // Update every minute
    });
</script>
@endpush