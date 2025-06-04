@props(['article'])
@php
    $wordCount = str_word_count(strip_tags($article->content ?? ''));
    $readingTime = ceil($wordCount / 238 );
    $readingTime = max(1, $readingTime); // Ensure minimum 1 minute read time
@endphp
<span class="nav-item"><i {{$attributes->merge(['class' => 'far fa-clock me-1'])}}></i>{{ $readingTime }} min read</span>
    {{-- class=""></i>{{ $readingTime }} min read</span> --}}