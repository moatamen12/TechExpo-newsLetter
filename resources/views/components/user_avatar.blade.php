@props(['user', 'class' => '', 'size' => 40])

<div class="avatar me-2 {{ $class }}">
    @if($user)
        @if($user->userProfile && $user->userProfile->profile_photo && Storage::disk('public')->exists($user->userProfile->profile_photo))
            {{-- Show profile photo if user has a profile --}}
            <img class="avatar-img rounded-circle avatar-img" 
                 style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;"
                 src="{{ asset('storage/' . $user->userProfile->profile_photo) }}" 
                 alt="avatar">
        @else
            {{-- Show initials if no profile photo or no profile --}}
            <div class="rounded-circle d-flex justify-content-center align-items-center avatar-letter" 
                 style="width: {{ $size }}px; height: {{ $size }}px; background-color: #6c757d; color: white; font-weight: bold;">
                {{ isset($user->name) ? strtoupper(substr($user->name, 0, 1)) : 'U' }}
            </div>
        @endif
    @else
        {{-- Fallback for null user --}}
        <div class="rounded-circle d-flex justify-content-center align-items-center avatar-letter" 
             style="width: {{ $size }}px; height: {{ $size }}px; background-color: #6c757d; color: white; font-weight: bold;">
            U
        </div>
    @endif
</div>