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

// Function to handle modal opening
function openImageModal(imgElement) {
    const detailsImages = JSON.parse(imgElement.getAttribute('data-details-images'));
    const modalImagesContainer = document.getElementById('modalImagesContainer');
    const modalHeader = document.querySelector('.modal-header');
    
    modalImagesContainer.innerHTML = ''; // Clear previous images

    // Set the modal header with the bundle name
    const bundleName = imgElement.getAttribute('data-bundle');
    modalHeader.textContent = 'Bundle Product Samples For: ' + bundleName;

    // Create and append images to the modal
    detailsImages.forEach(image => {
        const img = document.createElement('img');
        img.src = 'data:image/jpeg;base64,' + image; // Correctly set the image source
        img.className = 'modal-image'; // Add class for styling
        modalImagesContainer.appendChild(img);
    });

    // Show the modal
    const imageModal = document.getElementById('imageModal');
    imageModal.style.display = 'block'; // Show the modal
}



// Close modal functionality
document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('imageModal').style.display = 'none'; // Hide the modal
});

window.onclick = function(event) {
    const modal = document.getElementById('imageModal');
    if (event.target === modal) {
        modal.style.display = 'none'; // Hide the modal if clicking outside
    }
};

// Handle loading overlay
window.onload = function() {
    document.body.classList.remove('loading');
    document.getElementById('loadingOverlay').style.display = 'none';
};

// Additional listener for product images
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.open-modal').forEach(img => {
        img.addEventListener('click', function() {
            openImageModal(this); // Call the modal opening function
        });
    });
});
