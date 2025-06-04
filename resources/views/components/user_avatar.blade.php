@props(['user'])
<div class="avatar me-2">
    @if(isset($user) && isset($user->profile) && 
       $user->profile->profile_image && 
       Storage::disk('public')->exists($user->profile->profile_image))
        <img class="avatar-img rounded-circle avatar-img" 
             src="{{ asset('storage/' . $user->profile->profile_image) }}" 
             alt="avatar" >
    @else
        <div class="rounded-circle d-flex justify-content-center align-items-center avatar-letter" 
             style="width: 40px; height: 40px; background-color: #6c757d; color: white; font-weight: bold;">
            {{ isset($user) && isset($user->name) ? strtoupper(substr($user->name, 0, 1)) : 'A' }}
        </div>
    @endif
</div>