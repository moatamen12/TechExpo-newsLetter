@props(['user', 'class' => '', 'size' => 40])

<div class="avatar me-2 {{ $class }}">
    @if($user)
        @php
            // Check if user is an author with a profile
            $isAuthor = $user->userProfile && $user->userProfile->profile_photo;
            $hasProfilePhoto = $isAuthor && Storage::disk('public')->exists($user->userProfile->profile_photo);
            $firstLetter = isset($user->name) ? strtoupper(substr($user->name, 0, 1)) : 'U';
        @endphp
        
        @if($hasProfilePhoto)
            {{-- Show profile photo only for authors who have uploaded one --}}
            <img class="avatar-img rounded-circle" 
                 style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;"
                 src="{{ asset('storage/' . $user->userProfile->profile_photo) }}" 
                 alt="avatar">
        @else
            {{-- Show site logo format for readers and authors without profile photos --}}
            <div class="rounded-circle d-flex justify-content-center align-items-center user-avatar-logo" 
                 style="width: {{ $size }}px; height: {{ $size }}px;">
                <span class="logo-avatar-text">⟨{{ $firstLetter }}/⟩</span>
            </div>
        @endif
    @else
        {{-- Fallback for null user --}}
        <div class="rounded-circle d-flex justify-content-center align-items-center user-avatar-logo" 
             style="width: {{ $size }}px; height: {{ $size }}px;">
            <span class="logo-avatar-text">⟨U/⟩</span>
        </div>
    @endif
</div>