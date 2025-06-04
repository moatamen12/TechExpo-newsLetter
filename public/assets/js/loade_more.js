document.addEventListener('DOMContentLoaded', function () {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const articlesContainer = document.getElementById('latest-articles-container');
    let loading = false;

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            if (loading) return;
            loading = true;
            
            let page = parseInt(this.getAttribute('data-page'));
            const originalButtonText = this.innerHTML;
            
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            this.disabled = true;

            const loadMoreUrl = this.getAttribute('data-url') || '/load-more-articles';
            
            fetch(`${loadMoreUrl}?page=${page}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Debug info:', data.debug); // Remove this after testing
                
                if (data.html && data.html.trim() !== "") {
                    articlesContainer.insertAdjacentHTML('beforeend', data.html);
                    
                    // Update page number for next request
                    this.setAttribute('data-page', data.nextPage);
                    
                    // Hide button if no more pages
                    if (!data.hasMorePages) {
                        this.style.display = 'none';
                    } else {
                        // Reset button for next load
                        this.innerHTML = originalButtonText;
                        this.disabled = false;
                    }
                } else {
                    // No more articles
                    this.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading more articles:', error);
                this.innerHTML = 'Failed to load. Retry?';
                this.disabled = false;
            })
            .finally(() => {
                loading = false;
            });
        });
    }
});

