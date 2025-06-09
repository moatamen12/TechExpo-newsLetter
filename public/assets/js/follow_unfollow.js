/**
 * User Interactions JavaScript
 * Handles follow/unfollow, like/unlike, and save/unsave functionality
 */

class UserInteractions {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Follow/Unfollow functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.js-follow-button') || e.target.closest('.js-follow-button')) {
                e.preventDefault();
                this.handleFollowToggle(e.target.closest('.js-follow-button'));
            }
        });

        // Like/Unlike functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('#likeButton') || e.target.closest('#likeButton')) {
                e.preventDefault();
                this.handleLikeToggle(e.target.closest('#likeButton'));
            }
        });

        // Save/Unsave functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.js-save-article-button') || e.target.closest('.js-save-article-button')) { // Changed selector from #saveArticleButton to .js-save-article-button
                e.preventDefault();
                this.handleSaveToggle(e.target.closest('.js-save-article-button'));
            }
        });
    }

    async handleFollowToggle(button) {
        const isFollowing = button.dataset.isFollowing === 'true';
        const url = isFollowing ? button.dataset.unfollowUrl : button.dataset.followUrl;
        const method = isFollowing ? 'DELETE' : 'POST';

        // Disable button during request
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = isFollowing ? 'Unfollowing...' : 'Following...';

        try {
            const response = await this.makeRequest(url, method);
            
            if (response.success) {
                // Update button state
                const newIsFollowing = !isFollowing;
                button.dataset.isFollowing = newIsFollowing.toString();
                
                if (newIsFollowing) {
                    button.textContent = 'Unfollow'; // Changed from 'Following' to 'Unfollow'
                    button.className = 'btn secondary-btn rounded-pill me-2 js-follow-button'; 
                } else {
                    button.textContent = 'Follow';
                    button.className = 'btn secondary-btn rounded-pill me-2 js-follow-button';
                }

                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message || 'An error occurred', 'error');
                button.textContent = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
            button.textContent = originalText;
        } finally {
            button.disabled = false;
        }
    }

    async handleLikeToggle(button) {
        const isLiked = button.dataset.liked === 'true';
        const url = isLiked ? button.dataset.unlikeUrl : button.dataset.likeUrl;
        const method = 'POST';

        try {
            const response = await this.makeRequest(url, method);
            
            if (response.success) {
                // Update button state
                const newIsLiked = !isLiked;
                button.dataset.liked = newIsLiked.toString();
                
                const icon = button.querySelector('.like-icon');
                const countSpan = button.querySelector('.like-count');
                
                if (newIsLiked) {
                    icon.className = 'like-icon fa-solid fa-heart';
                } else {
                    icon.className = 'like-icon fa-regular fa-heart';
                }
                
                if (countSpan) {
                    countSpan.textContent = response.like_count || 0;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
        }
    }

    async handleSaveToggle(button) {
        const isSaved = button.dataset.saved === 'true';
        const url = isSaved ? button.dataset.unsaveUrl : button.dataset.saveUrl;
        const method = 'POST';

        try {
            const response = await this.makeRequest(url, method);
            
            if (response.success) {
                // Update button state
                const newIsSaved = !isSaved;
                button.dataset.saved = newIsSaved.toString();
                
                const icon = button.querySelector('.save-icon');
                
                if (newIsSaved) {
                    icon.className = 'save-icon fa-solid fa-bookmark';
                    button.title = 'Unsave article'; // Update title
                } else {
                    icon.className = 'save-icon fa-regular fa-bookmark';
                    button.title = 'Save article'; // Update title
                }

                this.showNotification(response.message, 'success');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
        }
    }

    async makeRequest(url, method = 'GET', data = {}) {
        const options = {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        if (method !== 'GET' && Object.keys(data).length > 0) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        return await response.json();
    }

    showNotification(message, type = 'info') {
        // Simple notification system - you can replace this with your preferred notification library
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new UserInteractions();
});

// Also expose globally for manual initialization if needed
window.UserInteractions = UserInteractions;