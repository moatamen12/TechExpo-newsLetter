<!-- subscrip -->
    <section class="sb-bg shadow-sm">
        <div class="container p-3 border-0">
            <div class="my-5">
                {{-- <img src="{{asset('assets\images\sub-bg.jpeg')}}" class="card-img" alt="..." style="height: 350px;"> --}}
                <div class="d-flex flex-column justify-content-center text-center" >
                    <h2 class=" card-title fw-bold mb-2">Stay Updated with Tech News!</h2>
                    <p class="card-text">Get the freshest headlines and updates sent uninterrupted to your inbox <br>writen by your following Authors in the style ypu like .</p>

                    <form class="row g-3 justify-content-center my-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="inputEmail" class="visually-hidden">Email</label>
                            <input type="email" class="form-control" id="inputEmail" placeholder="Enter Your Email!" required>
                        </div>
                        
                        <div class="col-auto">
                            <a class="btn btn-primary mb-3 btn-subscribe "  href="#" data-bs-toggle="modal" data-bs-target="#subscribeModal"><i class="fa-regular fa-envelope" class="me-2"></i>  Subscribe</a>
                        </div>
                    </form>
                    <p class="card-text"><small>By subscribing you agree to our <a href="" style="text-decoration: none;">Privacy Policy</a></small></p>
                </div>
            </div>
        </div>
    </section>