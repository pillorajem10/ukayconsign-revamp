// cxInfos.js
document.addEventListener('DOMContentLoaded', function() {
    // Open Modal
    const openModalButton = document.getElementById('openEmailBlastModal');
    const emailBlastModal = document.getElementById('emailBlastModal');
    const closeModalButton = document.getElementById('closeEmailBlastModal');

    openModalButton.addEventListener('click', function() {
        emailBlastModal.style.display = 'flex'; // Show the modal
    });

    // Close Modal
    closeModalButton.addEventListener('click', function() {
        emailBlastModal.style.display = 'none'; // Hide the modal
    });

    // Close modal if overlay is clicked
    emailBlastModal.addEventListener('click', function(event) {
        if (event.target === emailBlastModal) {
            emailBlastModal.style.display = 'none'; // Hide the modal
        }
    });
});

document.getElementById('interest_filter').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});