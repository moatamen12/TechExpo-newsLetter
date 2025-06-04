@props(['title', 'description', 'btn' => null])
<div {{$attributes->merge(['class'=>"d-flex align-items-center justify-content-between"])}}>
    <div class="my-4">
        <h1 {{$attributes->merge(['class'=> 'fw-bold'])}}> {{$title}} </h1>
        <footer {{$attributes->merge(['class'=> 'text-muted mt-2'])}}>{{$description}}</footer>
    </div>
    @if($btn)
        <div class="d-flex flex-column">
            {{-- Check if $btn is a single button associative array (has 'link' key and no numeric index 0) --}}
            @if(is_array($btn) && isset($btn['link']) && !isset($btn[0]))
                <a href="{{ $btn['link'] }}" class="btn btn-subscribe">
                    {!! $btn['text'] !!}
                </a>
            {{-- Else, if $btn is an array, assume it's an array of button arrays --}}
            @elseif(is_array($btn))
                @foreach($btn as $button)
                    {{-- Ensure each item in the loop is a valid button array --}}
                    @if(is_array($button) && isset($button['link']))
                        <a href="{{ $button['link'] }}" class="btn btn-lg btn-subscribe mb-2">
                            {!! $button['text'] !!}
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
    @endif
</div>