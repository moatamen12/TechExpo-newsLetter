@if(request()->is('dashboard*'))
    <x-dashboard-nav />
@else
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="{{route('home')}}">Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="{{route('articles')}}">Articles</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('about')}}">About</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('contact')}}">Contact</a>
    </li>
@endif