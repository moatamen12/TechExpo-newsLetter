<div class="flex flex-col items-center justify-center min-h-[400px] p-8 text-center">
    <!-- Updated svg container to center the svg -->
    <div class="mb-5">
        <!-- Updated first SVG size -->
        <svg fill="currentColor" viewBox="0 0 24 24" class="img-fluid mx-auto" style="width: 48px; height: 48px;">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
        </svg>
    </div>
    
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">
        No Articles Found
    </h2>
    
    <p class="text-gray-500 mb-6 max-w-md">
        We couldn't find any articles matching your criteria. Try adjusting your search or check back later for new content.
    </p>
    
    <div class="space-x-4">
        <a href="{{ route('home') }}" class="secondary-btn rounded inline-flex items-center px-4 py-2">
            <!-- Modified the "Back to Home" SVG icon size -->
            <svg fill="currentColor" viewBox="0 0 20 20" class="img-fluid me-2" style="width: 20px; height: 20px;">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            </svg>
            Back to Home
        </a>
        
        <a href="{{ url()->previous() }}" class="ms-2 inline-flex items-center px-4 py-2 border btn-subscribe rounded ">
            Clear Filters 
        </a>
    </div>
</div>