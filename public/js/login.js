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

        // Hide the promo modal after 5 seconds
        setTimeout(() => {
            promoModal.style.display = 'none'; // Hide the modal
        }, 2000); 
        */
    }, 100); // Adjust delay if necessary

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
});
