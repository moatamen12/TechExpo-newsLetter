document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            // Don't trigger if they clicked on a button or link in the actions column
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            window.location.href = this.dataset.href;
        });
    });
});
