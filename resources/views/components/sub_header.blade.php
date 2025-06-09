<section class="container py-2 rounded-2 ">
    <div class="d-flex justify-content-between align-items-center">        
        <div class="mx-2 flex-grow-1">
            <form action="{{ route('articles.search') }}" method="GET">
                <div class="position-relative input-group-sm">
                    <button class="btn border-0 position-absolute top-50 end-0 translate-middle-y me-2" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" fill="#6c757d" stroke="#6c757d" stroke-width="2">
                            <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                        </svg>
                    </button>
                    <input type="text" class="form-control rounded-4 pe-5" placeholder="Search articles by author or title..." name="q">

                </div>
            </form>
        </div>
        
        <!-- Right side - Filter dropdowns -->
        <div class="d-flex gap-2">
            <!-- Sort dropdown button -->
            <div class="dropdown">
                <button class="btn btn-sm btn-subscribe-outline bg-white dropdown-toggle" type="button" id="sortDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-sort me-1"></i> Sort by
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdownToggle">
                    <li><a class="dropdown-item" href="?sort=latest">Latest</a></li>
                    <li><a class="dropdown-item" href="?sort=trending">Trending <i class="fa-solid fa-arrow-trend-up" style="color: #3264ff;"></i></a></li>
                    <li><a class="dropdown-item" href="?sort=likes">By Like count <i class="fa-solid fa-heart" style="color: #ff6464;"></i></a></li>
                    <li><a class="dropdown-item" href="?sort=views">By Views Count <i class="fa-solid fa-eye" style="color: #74C0FC;"></i></a></li>
                </ul>
            </div>
            
            <!-- Category/filter dropdown button -->
            <div class="dropdown">
                <button class="btn btn-sm btn-subscribe-outline bg-white dropdown-toggle" type="button" id="filterDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                
                <ul class="dropdown-menu " aria-labelledby="filterDropdownToggle">
                    <li><h6 class="dropdown-header">Categories</h6></li>
                    @if(isset($filterCategories) && $filterCategories->count() > 0)
                        @foreach($filterCategories as $category)
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['category' => $category->name, 'page' => null]) }}">{{ $category->name }}</a></li>
                        @endforeach
                    @else
                        <li><a class="dropdown-item" href="#">No categories found</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['category' => 'all', 'page' => null]) }}">All Categories</a></li>
                    
                    <li><hr class="dropdown-divider"></li> 
                    
                    <li><h6 class="dropdown-header">Time Period</h6></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['period' => 'day', 'page' => null]) }}">Today</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['period' => 'week', 'page' => null]) }}">This Week</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['period' => 'month', 'page' => null]) }}">This Month</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['period' => 'year', 'page' => null]) }}">This Year</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['period' => 'all', 'page' => null]) }}">All Time</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
