@extends('layouts.app')
@section('title', 'Dashboard')

@php 
    $CreateBTN = [
        'link' => route('articles.create'),
        'text' => '<span><i class="fa-regular fa-pen-to-square" style="color: #ffffff;"></i> New Content</span>'
    ];
    $dashboardTabs=[
        'all' => [
            'activeTab' => 'all',
            'id' => 'all-tab',
            'ariaControls' => 'allContent',
            'txt' => 'All Articles'
        ],
        'published' => [
            'activeTab' => 'published',
            'id' => 'published-tab',
            'ariaControls' => 'publishedContent',
            'txt' => 'Published'
        ],
        'draft' => [
            'activeTab' => 'draft',
            'id' => 'draft-tab',
            'ariaControls' => 'draftContent',
            'txt' => 'Draft'
        ],
        // 'Scedueld' => [
        //     'activeTab' => 'Scedueld',
        //     'id' => 'Scedueld-tab',
        //     'ariaControls' => 'ScedueldContent',
        //     'txt' => 'Scedueld'
        // ],
    ];
    $activeTab = 'all'; // This should match the tab you want active by default
@endphp


@section('content')
<section class="container-fluid p-5">
    <div class="m-2">
        <x-dashboard-header 
            title="Articles " 
            description="All your Articles is in hear in one place to handele" 
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
                <h5 class="card-title fw-bold m-2">All Articles</h5>
                <div class="card-body mt-2">
                    <x-dashboard-table :vars="$allArticles" tableID="all-articles-table"/>
                </div>
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab == 'published' ? 'show active' : '' }}" 
        id="publishedContent" role="tabpanel" 
        aria-labelledby="published-tab" tabindex="0">

            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Published Articles</h5>
                <div class="card-body mt-2">
                    <x-dashboard-table :vars="$published" tableID="published-articles-table"/>
                </div>
            </div>
        </div>  

        <div class="tab-pane fade {{ $activeTab == 'draft' ? 'show active' : '' }}" 
        id="draftContent" role="tabpanel" aria-labelledby="draft-tab" tabindex="0">
            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Draft Articles</h5>
                <div class="card-body mt-2" >
                    <x-dashboard-table :vars="$draft" tableID="draft-articles-table"/>
                </div>
            </div>
        </div>

        {{-- <div class="tab-pane fade " id="ScedueldContent" role="tabpanel" aria-labelledby="Scedueld-tab" tabindex="0">
            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Sceduled Articles</h5>
                <div class="card-body mt-2">
                    <x-dashboard-table :vars="$archived" />
                    @if ($archived->hasMorePages())
                        <div class="text-center mt-3">
                            <button class="btn btn-primary dashboard-load-more-btn" 
                                    data-next-page-url="{{ $archived->nextPageUrl() }}" 
                                    data-table-target="#scheduled-articles-table tbody"
                                    data-type="Scheduled">
                                Load More Scheduled
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div> --}}
    </div>
</section>
@push('scripts')
@endpush
@endsection
