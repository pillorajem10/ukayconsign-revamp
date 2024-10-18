// public/js/login.js
function showLoading() {
    document.getElementById('logPageloadingOverlay').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const promoModal = document.getElementById('promoModal');
    const closeModal = document.getElementById('closeModal');

    // Hide the loading overlay after a slight delay
    setTimeout(() => {
        // Check if the promo modal has been shown before
        const promoData = sessionStorage.getItem('promoData');

        // Show the promo modal if it hasn't been shown before
        if (!promoData || (Date.now() - JSON.parse(promoData).timestamp > 900000)) {
            promoModal.style.display = 'flex'; // Show the modal
    
            // Hide the promo modal after 3 seconds
            setTimeout(() => {
                promoModal.style.display = 'none'; // Hide the modal
            }, 3000); // 3000 milliseconds = 3 seconds
    
            // Set flag in session storage with a timestamp
            sessionStorage.setItem('promoData', JSON.stringify({ timestamp: Date.now() }));
        }
    }, 100); // Adjust delay if necessary

    // Close modal when the close button is clicked

    /*
    closeModal.addEventListener('click', function() {
        promoModal.style.display = 'none'; // Hide the modal immediately
    });

    // Close modal when clicking outside of it
    promoModal.addEventListener('click', function(event) {
        if (event.target === promoModal) {
            promoModal.style.display = 'none'; // Hide the modal if clicking outside
        }
    });
    */
});

