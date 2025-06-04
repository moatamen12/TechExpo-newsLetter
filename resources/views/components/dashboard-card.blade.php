@props(['title', 'icon', 'count'])

<div {{$attributes->merge(['class' => 'card border-light p-1 mt-3 flex-fill border-1 border-light-subtle','style'=>'min-width: 200px; max-width: 300px;'])}}>
    <div class="card-body d-flex flex-column">
        <div {{$attributes->merge(['class'=>'text-muted mb-2 small'])}} >{{$title}}</div>
        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <div {{$attributes->merge(['class'=>'h4 fw-bold mb-0 icon'])}} style="line-height: 1; flex: 1; min-width: 0;">{{$count}}</div>
            <div class="p-1 flex-shrink-0" style="max-width: 40px; max-height: 40px; overflow: hidden;">
                <div class="card-icon" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                    {!! $icon !!}
                </div>
            </div>
        </div>
    </div>
</div>