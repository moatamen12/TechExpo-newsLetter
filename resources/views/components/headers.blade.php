@props(['title','description',
        'text'=>'See More',
        'link'=>'articles',
        'url'=>null])
        
<div class="d-flex align-items-center justify-content-between">
    <div class ="my-4">
        <h2 class="fw-bold"> {{$title}} </h2>
        <footer class="text-muted mt-2">{{$description}}</footer>
    </div>
    <div class=" mt-4">
            @if($url)
            <a href="{{ $url }}" class="btn btn-subscribe mx-3">{{ $text }}<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" class="ms-1" fill="white"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
        @else
            <a href="{{ route($link) }}" class="btn btn-subscribe mx-3">{{ $text }}<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" class="ms-1" fill="white"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
        @endif
    </div>
</div>