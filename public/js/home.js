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
});
