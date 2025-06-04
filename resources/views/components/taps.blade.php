@props(['taps','activeTab'])

<div>
    <div {{$attributes->merge(['class' => 'login-bg rounded-2 my-3 px-2  ', 'style' =>'width: fit-content' ])}} >
        <ul {{$attributes->merge(['class' => 'nav nav-pills m-2 p-1 d-flex justify-content-between', 'id' =>'pills-tab' ])}} role="tablist">
            @foreach ($taps as $tab)
                <li {{$attributes->merge(['class' => 'nav-item text-center me-2'])}} role="presentation">
                    <button class="nav-link px-2 {{ $activeTab == $tab['activeTab'] ? 'active ' : '' }} w-100" 
                        id="{{ $tab['id'] }}" 
                        data-bs-toggle="pill" 
                        data-bs-target="#{{ $tab['ariaControls'] }}" 
                        type="button" 
                        role="tab" 
                        aria-controls="{{ $tab['ariaControls'] }}" 
                        aria-selected="{{ $activeTab == $tab['activeTab'] ? 'true' : 'false' }}">
                        {{ $tab['txt'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>