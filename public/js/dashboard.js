document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const promoModal = document.getElementById('promoModal');
    const closeModal = document.getElementById('closeModal');
    const showPromosButton = document.getElementById('showPromosButton');

    // Show the loading overlay when the page starts loading
    loadingOverlay.style.display = 'flex'; // Ensure it's visible and centered

    // Hide the loading overlay after a slight delay
    setTimeout(() => {
        loadingOverlay.style.display = 'none'; // Hide overlay

        // Check if the promo modal has been shown before
        const hasShownPromo = localStorage.getItem('hasShownPromo');

        // Show the promo modal if it hasn't been shown before

        if (!hasShownPromo) {
            promoModal.style.display = 'flex'; // Show the modal

            // Hide the promo modal after 5 seconds
            setTimeout(() => {
                promoModal.style.display = 'none'; // Hide the modal
            }, 3000); // 5000 milliseconds = 5 seconds

            // Set flag in local storage
            localStorage.setItem('hasShownPromo', 'true'); 
        }

        /*
        promoModal.style.display = 'flex'; // Show the modal

        setTimeout(() => {
            promoModal.style.display = 'none'; // Hide the modal
        }, 2000); // 5000 milliseconds = 5 seconds
        */

        // Apply slide-in classes to dashboard containers
        const dashboardContainers = document.querySelectorAll('.dashboard-container');
        dashboardContainers.forEach((container, index) => {
            if (index % 2 === 0) {
                container.classList.add('slide-in-left');
            } else {
                container.classList.add('slide-in-right');
            }
        });
    }, 100);

    // Close modal when the close button is clicked
    closeModal.addEventListener('click', function() {
        promoModal.style.display = 'none'; // Hide the modal immediately
    });

    // Close modal when clicking outside of it
    promoModal.addEventListener('click', function(event) {
        if (event.target === promoModal) {
            promoModal.style.display = 'none'; // Hide the modal if clicking outside
        }
    });

    // Show the promo modal when the button is clicked
    showPromosButton.addEventListener('click', function() {
        promoModal.style.display = 'flex'; // Show the modal
    });
});
