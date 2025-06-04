<!-- Latest Articles -->
<section class="p-4 container">
    <div class="my-5">
        <x-headers 
            title="Latest Articles"
            description="Latest cutting-edge tech insights and innovations to keep you informed in our fast-paced digital world."
        />
        {{-- search bar --}}
        <div class="my-3">
            <x-sub_header/>
        </div>
        <div class="row row-cols-sm-1 row-cols-lg-3 row-cols-md-2 g-4" id="latest-articles-container">  
            @forelse($articles as $article)
                <x-card_vertical :article="$article" />
            @empty
                <div class="col-12 text-center">
                    <x-no-articles-found />
                </div>
            @endforelse
        </div>
    </div>
    {{-- Pagination --}}
    @if(isset($articles) && $articles->hasMorePages())
        <div class="d-flex align-items-center justify-content-center text-center my-3">
            <button id="load-more-btn" 
                    data-page="{{ $articles->currentPage() + 1 }}" 
                    data-url="{{ route('home.loadMoreArticles') }}"
                    class="btn btn-lg btn-subscribe-outline shadow-sm mx-auto">
                Load More   
            </button>
        </div>
    @endif
</section>

@push('scripts')
    <script src="{{ asset('assets/js/loade_more.js') }}"></script>
@endpush