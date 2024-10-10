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
        const promoData = sessionStorage.getItem('promoData');
    
        if (!promoData || (Date.now() - JSON.parse(promoData).timestamp > 900000)) {
            promoModal.style.display = 'flex'; // Show the modal
    
            // Hide the promo modal after 3 seconds
            setTimeout(() => {
                promoModal.style.display = 'none'; // Hide the modal
            }, 3000); // 3000 milliseconds = 3 seconds
    
            // Set flag in session storage with a timestamp
            sessionStorage.setItem('promoData', JSON.stringify({ timestamp: Date.now() }));
        }
    
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
