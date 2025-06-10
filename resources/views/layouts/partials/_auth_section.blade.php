@props(['context' => 'navbar']) {{-- 'navbar' or 'offcanvas' --}}

@guest
    @if ($context === 'navbar')
        <a class="btn btn-subscribe rounded-3" href="{{route('subscribe')}}">Subscribe</a>
        <a class="btn secondary-btn ms-2 rounded-3" href="{{route('login')}}">LogIn</a>
    @else {{-- offcanvas --}}
        <div class="d-grid gap-2">
            <a class="btn btn-subscribe rounded-3" href="{{route('subscribe')}}">Subscribe</a>
            <a class="btn secondary-btn rounded-3" href="{{route('login')}}">LogIn</a>
        </div>
    @endif
@endguest

@auth
    @if ($context === 'navbar')
        <div class="d-flex align-items-center">
            @if(Auth::user()->userProfile()->exists())
                <div class="me-2">
                    <a href="{{route('dashboard')}}" class="btn btn-subscribe rounded-3">Dashboard</a>
                </div>
            @else
                <div class="me-2">
                    <a href="{{route('profile')}}" class="btn btn-subscribe rounded-3"><i class="fa-regular fa-pen-to-square me-2" ></i> Write</a>
                </div>
            @endif

            <div class="dropdown">
                <button class="btn btn-link p-0" type="button" id="userDropdownNavbar" data-bs-toggle="dropdown" aria-expanded="false">
                    <x-user_avatar :user="Auth::user()" size="40" class="interactive" />
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownNavbar">
                    <li class="px-3 py-1 text-muted small">Signed in as <br><strong>{{ Auth::user()->name }}</strong></li>
                    <li><hr class="dropdown-divider"></li>
                    
                    @if(Auth::user()->userProfile()->exists())
                        {{-- Author dropdown options --}}
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">My Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profile Settings</a></li>
                    @else
                        {{-- Reader dropdown options --}}
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                    @endif
                    
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Log Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    @else {{-- offcanvas --}}
        <div class="dropdown mb-3">
            <button class="btn btn-outline-secondary dropdown-toggle w-100 d-flex align-items-center text-start p-2" type="button" id="userDropdownOffcanvas" data-bs-toggle="dropdown" aria-expanded="false">
                {{-- Replace with custom avatar component --}}
                <x-user_avatar :user="Auth::user()" size="30" class="interactive me-2" />
                <span class="flex-grow-1">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu w-100" aria-labelledby="userDropdownOffcanvas">
                <li class="px-3 py-1 text-muted small">Signed in as <br><strong>{{ Auth::user()->name }}</strong></li>
                <li><hr class="dropdown-divider"></li>
                
                @if(Auth::user()->userProfile()->exists())
                    {{-- Author dropdown options --}}
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">My Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile') }}">Profile Settings</a></li>
                @else
                    {{-- Reader dropdown options --}}
                    <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                @endif
                
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
        <div class="d-grid">
            @if(Auth::user()->userProfile()->exists())
                <a href="{{route('dashboard')}}" class="btn btn-subscribe">Go to Dashboard</a>
            @else
                <a href="{{route('profile')}}" class="btn btn-subscribe">Create Profile</a>
            @endif
        </div>
    @endif
@endauth