@props(['imageURL'])
@if($imageURL && Storage::disk('public')->exists($imageURL))
    <img class="rounded-3 w-100 h-100 object-fit-cover" 
        src="{{ asset('storage/' . $imageURL)}}" >
        {{-- alt="{{ $featuredArticle->title }}"> --}}
@else
    <div class="d-flex align-items-center justify-content-center h-100 w-100 image_placholder rounded-3">
        <div class="aiph fs-1 text-secondary fw-bold">⟨/⟩</div>
    </div>
@endif

