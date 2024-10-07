document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Show the loading overlay when the page starts loading
    loadingOverlay.style.display = 'flex'; // Ensure it's visible and centered

    // Hide the loading overlay after a slight delay to ensure smooth transition
    setTimeout(() => {
        loadingOverlay.style.display = 'none'; // Hide overlay

        // Apply slide-in classes after the overlay is hidden
        const promoContainer = document.querySelector('.promo-container');
        const dashboardContainers = document.querySelectorAll('.dashboard-container');

        promoContainer.classList.add('slide-in-left');

        dashboardContainers.forEach((container, index) => {
            if (index % 2 === 0) {
                container.classList.add('slide-in-left');
            } else {
                container.classList.add('slide-in-right');
            }
        });
    }, 100); // Adjust delay if necessary
});
