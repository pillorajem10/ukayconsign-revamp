document.addEventListener('DOMContentLoaded', function() {
    // Apply blur to the body
    document.body.classList.add('blurred');

    // Hide loading spinner after a short delay (simulating loading)
    setTimeout(function() {
        // Hide the loading overlay
        document.getElementById('loading').style.display = 'none';

        // Remove blur from the body
        document.body.classList.remove('blurred');

        // Show the content
        const content = document.getElementById('content');
        content.style.display = 'block';

        // Animate each promo item
        const promoItems = document.querySelectorAll('.promo-item');
        promoItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('show'); // Trigger the slide-in animation
            }, index * 300); // Stagger the animations
        });
    }, 1000); // Adjust the timeout as needed
});
