@props(['user', 'size' => 40, 'class' => ''])

@php
    $initials = isset($user) && isset($user->name) ? strtoupper(substr($user->name, 0, 1)) : '?';
    $hasImage = isset($user) && isset($user->profile) && 
                isset($user->profile->avatar) && 
                Storage::disk('public')->exists($user->profile->avatar);
    $bgColor = $hasImage ? '' : '#f4f4f5';
@endphp

<div class="avatar-wrapper {{ $class }}" style="width: {{ $size }}px; height: {{ $size }}px;">
    @if($hasImage)
        <img 
            src="{{ asset('storage/' . $user->profile->avatar) }}" 
            alt="{{ $user->name }}" 
            class="rounded-circle" 
            style="width: 100%; height: 100%; object-fit: cover;"
        >
    @else
        <div 
            class="rounded-circle d-flex justify-content-center align-items-center" 
            style="width: 100%; height: 100%; background-color: {{ $bgColor }}; color: black; font-weight: bold; font-size: {{ $size / 2 }}px;"
        >
            {{ $initials }}
        </div>
    @endif
</div>