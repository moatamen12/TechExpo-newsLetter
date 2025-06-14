@extends('layouts.app')
@section('title', 'Newsletter Management')

@php
    $CreateBTN = [
        'link' => route('newsletter.create'),
        'text' => '<span><i class="fa-regular fa-pen-to-square" style="color: #ffffff;"></i> Create Newsletter</span>'
    ];
    $dashboardTabs=[
        'all' => [
            'activeTab' => 'all',
            'id' => 'all-tab',
            'ariaControls' => 'allContent',
            'txt' => 'All Newsletters'
        ],
        'draft' => [
            'activeTab' => 'draft',
            'id' => 'draft-tab',
            'ariaControls' => 'draftContent',
            'txt' => 'Draft'
        ],
        'scheduled' => [
            'activeTab' => 'scheduled',
            'id' => 'scheduled-tab',
            'ariaControls' => 'scheduledContent',
            'txt' => 'Scheduled'
        ],
        'sent' => [
            'activeTab' => 'sent',
            'id' => 'sent-tab',
            'ariaControls' => 'sentContent',
            'txt' => 'Sent'
        ],
    ];
    $activeTab = 'all'; // This should match the tab you want active by default
@endphp

@section('content')
<section class="container-fluid p-5">
    <div class="m-2">
        <x-dashboard-header 
            title="Newsletter Management" 
            description="Create, edit, and send newsletters to your followers" 
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

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <x-taps :taps="$dashboardTabs" :activeTab="$activeTab" />
        {{-- search --}}
        <div class="w-50 d-flex justify-content-end bg-white shadow-sm rounded-5">
            <x-sub_header/>
        </div>
    </div>  
    
    <div class="tab-content" id="pills-tabContent">

        <div class="tab-pane fade {{ $activeTab == 'all' ? 'show active' : '' }}" 
        id="allContent" role="tabpanel" 
        aria-labelledby="all-tab" tabindex="0">

            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">All Newsletters</h5>
                <div class="card-body mt-2">
                    <x-newsletter-table :newsletters="$allNewsletters" tableID="all-newsletters-table"/>
                </div>
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab == 'draft' ? 'show active' : '' }}" 
        id="draftContent" role="tabpanel" aria-labelledby="draft-tab" tabindex="0">
            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Draft Newsletters</h5>
                <div class="card-body mt-2">
                    <x-newsletter-table :newsletters="$draftNewsletters" tableID="draft-newsletters-table"/>
                </div>
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab == 'scheduled' ? 'show active' : '' }}" 
        id="scheduledContent" role="tabpanel" 
        aria-labelledby="scheduled-tab" tabindex="0">

            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Scheduled Newsletters</h5>
                <div class="card-body mt-2">
                    <x-newsletter-table :newsletters="$scheduledNewsletters" tableID="scheduled-newsletters-table"/>
                </div>
            </div>
        </div>  

        <div class="tab-pane fade {{ $activeTab == 'sent' ? 'show active' : '' }}" 
        id="sentContent" role="tabpanel" 
        aria-labelledby="sent-tab" tabindex="0">

            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Sent Newsletters</h5>
                <div class="card-body mt-2">
                    <x-newsletter-table :newsletters="$sentNewsletters" tableID="sent-newsletters-table"/>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
@endpush
@endsection