@extends('layouts.app')
@section('title', 'Dashboard')

@php 
    $CreateBTN = [
        'link' => 'articles/create',
        'text' => '<span><i class="fa-regular fa-pen-to-square" style="color: #ffffff;"></i> New Content</span>'
    ];
    $dashboardTabs=[
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
    $activeTab = 'published'; // This should match the tab you want active by default
@endphp


@section('content')
<section class="container-fluid p-5">
    <div class="m-2">
        <x-dashboard-header 
            title="Your Articles and NewsLetters" 
            description="All your Content is in hear in one place to handele" 
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
        <div class="w-50 d-flex justify-content-end">
            <x-sub_header/>
        </div>
    </div>  
    
    <div class="tab-content" id="pills-tabContent">

        <div class="tab-pane fade {{ $activeTab == 'published' ? 'show active' : '' }}" 
        id="publishedContent" role="tabpanel" 
        aria-labelledby="published-tab" tabindex="0">

            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Published Articles</h5>
                <div class="card-body mt-2">
                    <x-dashboard-table :vars="$published" tableID="published-articles-table"/>
                    @if ($published->hasMorePages())
                        <div class="text-center mt-3">
                            <button class="btn btn-subscribe-outline dashboard-load-more-btn" 
                                    data-next-page-url="{{ $published->nextPageUrl() }}" 
                                    data-table-target="#published-articles-table tbody" {{-- Target tbody of the table --}}
                                    data-type="Published">
                                Load More Published
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>  

        <div class="tab-pane fade {{ $activeTab == 'draft' ? 'show active' : '' }}" 
        id="draftContent" role="tabpanel" aria-labelledby="draft-tab" tabindex="0">
            <div class="card border-ligt p-2">
                <h5 class="card-title fw-bold m-2">Draft Articles</h5>
                <div class="card-body mt-2" >
                    <x-dashboard-table :vars="$draft" tableID="draft-articles-table" />
                    @if ($draft->hasMorePages())
                        <div class="text-center mt-3">
                            <button class="btn  btn-subscribe-outline dashboard-load-more-btn" 
                                    data-next-page-url="{{ $draft->nextPageUrl() }}" 
                                    data-table-target="#draft-articles-table tbody"
                                    data-type="Draft">
                                Load More Drafts
                            </button>
                        </div>
                    @endif
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.dashboard-load-more-btn').forEach(button => {
            // Store original button text if you want to reset it exactly
            // const originalButtonText = button.innerHTML; 

            button.addEventListener('click', function () {
                let nextPageUrl = this.dataset.nextPageUrl;
                const tableTargetSelector = this.dataset.tableTarget;
                const articlesContainer = document.querySelector(tableTargetSelector); // This should be the <tbody>
                const buttonEl = this;
                const buttonType = this.dataset.type || 'Items'; // Fallback type

                // Add right after the querySelector line
                console.log('Target selector:', tableTargetSelector);
                console.log('Found container:', articlesContainer);

                if (!nextPageUrl || !articlesContainer) {
                    console.error('Load more button is missing URL or target container cannot be found.');
                    buttonEl.style.display = 'none';
                    return;
                }

                const originalButtonText = `Load More ${buttonType}`; // Reconstruct original text
                buttonEl.disabled = true;
                buttonEl.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading ${buttonType}...`;

                fetch(nextPageUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status); // Debug
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data); // Debug the response
                    
                    if (data.html && data.html.trim() !== "") {
                        articlesContainer.insertAdjacentHTML('beforeend', data.html);
                    }

                    if (data.has_more_pages && data.next_page_url) {
                        buttonEl.dataset.nextPageUrl = data.next_page_url; // Update for the next click
                        buttonEl.disabled = false;
                        buttonEl.innerHTML = originalButtonText;
                    } else {
                        buttonEl.style.display = 'none'; // No more pages, hide the button
                    }
                })
                .catch(error => {
                    console.error(`Error loading more ${buttonType}:`, error);
                    buttonEl.disabled = false;
                    buttonEl.innerHTML = `Error loading. Retry ${buttonType}?`;
                });
            });
        });
    });
</script>
@endpush
@endsection
