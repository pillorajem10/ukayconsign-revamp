function hideMessage(elementId, duration) {
    const messageElement = document.getElementById(elementId);
    if (messageElement) {
        setTimeout(function() {
            messageElement.style.display = 'none';
        }, duration);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Call the hideMessage function for the success message
    hideMessage('success-message', 2000);
    hideMessage('error-message', 2000);

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