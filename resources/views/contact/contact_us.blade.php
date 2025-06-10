@extends('layouts.app')
@section('title', 'About Us')
@section('content')
<div class="container py-5 my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="fw-bold mb-2">Contact Us</h1>
            <p class="text-muted">Have questions, comments or suggestions? We'd love to hear from you!</p>
        </div>
    </div>


    {{-- user needs to be logged into be apple to send us a message --}}
    @guest
        {{-- Check if the specific 'auth_required' error exists --}}
        @php
            $authErrorClass = $errors->has('auth_required') ? 'alert-danger' : 'alert-info';
        @endphp
        <div class="alert {{ $authErrorClass }}">
            <h5>Login Required</h5>
            {{-- <p>You need to be logged in to send us a message.</p> --}}
            @if ($errors->has('auth_required'))
                <p class="fw-bold">{{ $errors->first('auth_required') }}</p>
            @else
                <p>You need to be logged in to send us a message.</p>
            @endif
                <a class="btn btn-subscribe rounded-3" href="{{route('subscribe')}}">Subscribe</a>
                <a class="btn secondary-btn ms-2 rounded-3" href="{{route('login')}}">LogIn</a>
        </div>
    @endguest

    {{-- display the succses message --}}
    @if (session('success'))
        <div class="alert alert-success" id="successMessage">
            <h5>Message Sent</h5>
            <p>{{ session('success') }} We will get back to you as soon as possible.</p>
        </div>
    @endif

    <!-- Contact form - only shown for logged in users -->
    <div class="row g-4 p-lg-5 p-sm-1">
        <!-- Form Column -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 rounded-3 shadow-sm">
                <div class="card-body ">
                    <h3 class="mb-4 border-start border-4 border-info ps-3">Send us a message</h3>
                    <form action="{{route('contact.submit')}}" method="post" id="contactForm">
                        @csrf
                        {{-- <input type="hidden" name="form_submitted" value="1" /> --}}
                        <div class="row mb-3 g-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="contact_username" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="contact_username" name="contact_username" 
                                       value="{{ old('contact_username') }}" placeholder="Enter your name"
                                       maxlength="80" minlength="5" required/>
                                <x-error_msg field="contact_username" />
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}" placeholder="Enter your email"
                                       maxlength="80" minlength="5" required/>
                                <x-error_msg field="email" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                       value="{{ old('subject') }}" placeholder="Tel us what yourmessage about"
                                       maxlength="100" minlength="5" required/>
                                <x-error_msg field="subject" />
                            </div>

                            {{-- catagory --}}
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="Technical Support" {{ old('category') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                                    <option value="complaint" {{ old('category') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                                    <option value="Suggestion" {{ old('category') == 'Suggestion' ? 'selected' : '' }}>Suggestion</option>
                                </select>
                                <x-error_msg field="category" />
                            </div>
                        </div>
                        {{-- message --}}
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control" id="message" name="message" id="contact-msg" 
                                      minlength="10" maxlength="500" rows="5" 
                                      placeholder="Please enter your message/complaint/suggestion here." required>{{ old('message') }}</textarea>
                            <footer class="text-muted">should not pass 500 letters</footer>
                            <x-error_msg field="message" />
                        </div>           
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
                            <button type="reset" class="btn btn-outline-info ms-2"><i class="fas fa-redo me-2"></i>Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Contact Info Column -->
        <div class="col-lg-12 ">
            <!-- Contact Information Card -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Contact Information</h5>
                    <div class="mb-3 d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-envelope fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Email Us</h6>
                            <a href="mailto:info@newsletter.com" class="text-decoration-none">info@newsletter.com</a>
                        </div>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-phone-alt fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Call Us</h6>
                            <a href="tel:+1234567890" class="text-decoration-none">+1 (234) 567-890</a>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Our Location</h6>
                            <p class="mb-0">123 Tech Street, Digital City, 45678</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- <!-- Social Media Card -->
            <div class="card shadow-sm shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Connect With Us</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none d-flex align-items-center">
                                <i class="fab fa-facebook-f me-3 text-primary"></i>
                                <span>@@facebook.com/newsletter</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none d-flex align-items-center">
                                <i class="fab fa-twitter me-3 text-info"></i>
                                <span>@@newsletter</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none d-flex align-items-center">
                                <i class="fab fa-instagram me-3 text-danger"></i>
                                <span>@@newsletter_official</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none d-flex align-items-center">
                                <i class="fab fa-linkedin me-3 text-primary"></i>
                                <span>@@linkedin.com/company/newsletter</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none d-flex align-items-center">
                                <i class="fa-brands fa-discord me-3" style="color: #794f9c;"></i>
                                <span>@@descord.com/newsletter</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div> --}}
        </div>
    </div>
</div>
<x-subscribe-footer-card />
@endsection