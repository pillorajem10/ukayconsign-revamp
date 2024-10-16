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
    hideMessage('success-message', 9000);
    hideMessage('error-message', 9000);

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