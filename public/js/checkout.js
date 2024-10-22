function showLoading() {
    document.getElementById('checkoutLoadingOverlay').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const termsCheckbox = document.getElementById('terms');
    const submitButton = document.getElementById('submit-button');
    const termsLink = document.querySelector('a[href="#terms-conditions"]');
    const termsModal = document.getElementById('terms-modal');

    // Initial state
    submitButton.disabled = !termsCheckbox.checked;

    // Toggle button state based on checkbox
    termsCheckbox.addEventListener('change', function() {
        submitButton.disabled = !this.checked;
    });

    // Open modal when terms link is clicked
    termsLink.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        termsModal.style.display = 'block';
    });

    // Close modal when the close button is clicked
    document.querySelector('.close-button').addEventListener('click', function() {
        termsModal.style.display = 'none';
    });

    // Close modal when clicking outside of the modal content
    window.addEventListener('click', function(event) {
        if (event.target === termsModal) {
            termsModal.style.display = 'none';
        }
    });
});
