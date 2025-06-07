@extends('layouts.app')
@section('title', 'Login & Subscribe')
@section('content')
    <section class="container-fluid rounded-3">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm  m-3 p-3">
                        <div class="text-center">
                            <div class="logo display-5 my-1" style="color: #229799">
                                <span class="text-3xl mr-1 r ">⟨</span>TechExpo<span class=" text-3xl ml-1">/⟩</span>
                            </div>
                                @if(session('error_gate'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-lock me-2"></i><strong>Access Denied:</strong> {{ session('error_gate') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                            <p class="text-muted">Your source for tech insights and news</p>
                        </div>
                        {{-- nav --}}
                        <div class="login-bg rounded-2 my-3 px-2 ">
                            <ul class="nav nav-pills m-1 d-flex justify-content-between" id="pills-tab" role="tablist">
                                {{-- login --}}
                                <li class="nav-item text-center flex-fill" role="presentation">
                                    <button class="nav-link {{ $activeTab == 'login' ? 'active' : '' }} w-100" id="loginForm-tab" data-bs-toggle="pill" data-bs-target="#loginForm" type="button" role="tab" aria-controls="loginForm" aria-selected="true">Login</button>
                                </li>

                                {{-- subscribe --}}
                                <li class="nav-item text-center flex-fill" role="presentation">
                                    <button class="nav-link {{ $activeTab == 'subscribe' ? 'active' : '' }} w-100" id="subscribeForm-tab" data-bs-toggle="pill" data-bs-target="#subscribeForm" type="button" role="tab" aria-controls="subscribeForm" aria-selected="false">Subscribe</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="pills-tabContent">
                            {{-- login form --}}
                            <div class="tab-pane fade {{ $activeTab == 'login' ? 'show active' : '' }}" id="loginForm" role="tabpanel" aria-labelledby="loginForm-tab" tabindex="0">
                                <div class="card border-0">
                                    <div class="px-3 pb-3">
                                        <form method="POST" action="{{ route('login.submit') }}" class="row g-2" id="loginForm">
                                            @csrf
                                            <!-- Email -->
                                            <div class="col-md-12">
                                                <label for="email" class="form-label mt-2 text-start">Email<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm " 
                                                       id="email"  name="email" autocomplete="email"
                                                        placeholder="Your@email.com" aria-required="true"
                                                        value="{{old('email')}}" required>

                                                <x-error_msg field="email"/> {{-- error display --}}
                                            </div>
            
                                            <!-- password -->
                                            <div class="col-md-12">
                                                <label for="password" class="form-label mt-2">Password<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control form-control-sm " id="password" 
                                                           placeholder="........" aria-required="true" 
                                                           name="password" autocomplete="new-password"  required>
                                                    <x-visibility-toggle/>
                                                </div>
                                                <x-error_msg field="password"/> {{-- error display --}}
                                            </div>

                                            <div class="form-check">
                                                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="remember">
                                                <label for="remember" class="form-check-label"> Remember me</label>
                                            </div>
                                                                        
                                            <div class="col-12 text-center d-flex flex-column justify-content-center mt-3">
                                                <button type="submit" class="btn btn-primary btn-subscribe text-center my-t">Login</button>
                                            </div>

                                            @include('components.google_login')
                                        </form>
                                    </div>
                                </div>
                            </div>


                            {{-- subscribe form --}}
                            <div class="tab-pane fade {{ $activeTab == 'subscribe' ? 'show active' : '' }}" id="subscribeForm" role="tabpanel" aria-labelledby="subscribeForm-tab" tabindex="0">
                                <div class="card border-0">
                                    <div class="px-3 pb-3">
                    
                                        <form action="{{ route('subscribe.submit') }}" method="POST" class="row g-3" id = "SubForm">
                                            @csrf
                                            <!-- Name -->
                                            <div class="col-md-12">
                                                <label for="name" class="form-label mt-2">name<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="name"
                                                       id="name" placeholder="Your Full Name" aria-required="true" 
                                                       autocomplete="name" value="{{old('name')}}" required>
                                                <x-error_msg field="name"/> {{-- error display --}}
                                            </div>
            
                                            <!-- Email -->
                                            <div class="col-md-12">
                                                <label for="Subemail" class="form-label mt-2">Email<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" id="Subemail" name="Subemail"
                                                       placeholder="your@email.com" aria-required="true" 
                                                       autocomplete="email" value="{{old('Subemail')}}" required>
                                                <x-error_msg field="Subemail"/> {{-- error display --}}                    
                                            </div>
            
                                            <!-- password -->
                                            <div class="col-md-12">
                                                <label for="Subpassword" class="form-label mt-2">Password<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control form-control-sm" id="Subpassword" 
                                                           placeholder="........" aria-required="true" name="Subpassword"
                                                           autocomplete="new-password" required>
                                                    <x-visibility-toggle/>
                                                </div>
                                                <footer class="text-muted">Must be at least 8 characters</footer>
                                                <x-error_msg field="Subpassword"/> {{-- error display --}}
                                            </div>
            
                                            <!-- confirm password -->
                                            <div class="col-md-12">
                                                <label for="Subpassword_confirmation" class="form-label mt-2">confirm Password<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control form-control-sm" id="Subpassword_confirmation" name="Subpassword_confirmation" 
                                                           placeholder="........" aria-required="true" autocomplete="new-password" required>
                                                    <x-visibility-toggle/>
                                                </div>
                                                <footer class="text-muted">Must be at least 8 characters</footer>
                                                <x-error_msg field="Subpassword_confirmation"/> {{-- error display --}}

                                            </div>
                                                                        
                                            <div class="col-12 text-center d-flex flex-column justify-content-center mt-3">
                                                <button type="submit" class="btn btn-subscribe text-center">Subscribe</button>
                                            </div>
            
                                            @include('components.google_login')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{asset('assets/js/visability.js')}}" defer></script>
@endsection