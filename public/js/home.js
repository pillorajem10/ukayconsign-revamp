function hideMessage(elementId, duration) {
    const messageElement = document.getElementById(elementId);
    if (messageElement) {
        setTimeout(function() {
            messageElement.style.display = 'none';
        }, duration);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const promoModal = document.getElementById('promoModal');
    const closeModal = document.getElementById('closeModal');
    // Call the hideMessage function for the success message
    hideMessage('success-message', 2000);
    hideMessage('error-message', 2000);

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

    const cards = document.querySelectorAll('.product-section-card');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target); // Stop observing once visible
            }
        });
    });

    cards.forEach(card => {
        observer.observe(card);
    });
});

window.onload = function() {
    document.body.classList.remove('loading');
    document.getElementById('loadingOverlay').style.display = 'none';
};