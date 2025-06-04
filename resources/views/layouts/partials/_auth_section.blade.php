@props(['context' => 'navbar']) {{-- 'navbar' or 'offcanvas' --}}

@guest
    @if ($context === 'navbar')
        <a class="btn btn-subscribe rounded-3" href="     {{route('subscribe')}}">Subscribe</a>
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
            <div class="me-2">
                <a href="{{route('dashboard')}}" class="btn btn-subscribe rounded-3">Dashboard</a>
            </div>
            <div class="dropdown">
                <button class="btn btn-link p-0" type="button" id="userDropdownNavbar" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-circle" style="width: 40px; height: 40px; border-radius: 50%; background-color: #6c757d; color: white; display: flex; justify-content: center; align-items: center; font-weight: bold; overflow: hidden;">
                        @if(Auth::user()->profile && Auth::user()->profile->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownNavbar">
                    <li class="px-3 py-1 text-muted small">Signed in as <br><strong>{{ Auth::user()->name }}</strong></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">My Dashboard</a></li>
                    <li><a class="dropdown-item" href="#">Profile Settings</a></li>
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
                <span class="avatar-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px; border-radius: 50%; background-color: #6c757d; color: white; font-weight: bold; overflow: hidden; flex-shrink: 0;">
                    @if(Auth::user()->profile && Auth::user()->profile->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </span>
                <span class="flex-grow-1">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu w-100 " aria-labelledby="userDropdownOffcanvas">
                <li class="px-3 py-1 text-muted small">Signed in as <br><strong>{{ Auth::user()->name }}</strong></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('dashboard') }}">My Dashboard</a></li>
                <li><a class="dropdown-item" href="#">Profile Settings</a></li>
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
            <a href="{{route('dashboard')}}" class="btn btn-subscribe">Go to Dashboard</a>
        </div>
    @endif
@endauth