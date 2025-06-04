document.addEventListener('DOMContentLoaded', function() {
    const likeButton = document.getElementById('likeButton');
    const likeIcon = likeButton.querySelector('.like-icon');
    
    likeButton.addEventListener('click', function() {
        // Toggle between solid and regular classes
        if (likeIcon.classList.contains('fa-regular')) {
            likeIcon.classList.remove('fa-regular');
            likeIcon.classList.add('fa-solid');
        } else {
            likeIcon.classList.remove('fa-solid');
            likeIcon.classList.add('fa-regular');
        }
    });
});