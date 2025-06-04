
<section class="p-4 container ">
    <div class="d-flex align-items-center justify-content-between ">
        <div class = "my-4">
            <h2 class=" fw-bold">Top Articles This Week</h2> <!-- Changed title -->
            <footer class="text-muted mb-4">Explore the most read and highly-rated articles from the past week, curated for you.</p> <!-- Changed description -->
        </div>
        @include ('components.seeMore') <!-- See more button -->
    </div>

    <div class="row row-cols-1 row-cols-md-4 g-4"> 
        <div class="col"> 
            @include ('components.card_vertical')
        </div>
    </div>  
</section>