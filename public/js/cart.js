// Handle checkbox selection
document.querySelectorAll('.bundle-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const bundleName = this.getAttribute('data-bundle');
        const checkboxes = document.querySelectorAll(`input[data-bundle="${bundleName}"]`);

        checkboxes.forEach(cb => {
            cb.checked = this.checked; // Check/uncheck all checkboxes with the same bundle
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const deleteButton = document.getElementById('delete-button');
    const checkoutButton = document.getElementById('checkout-button');
    

    // Function to toggle the delete button based on checked checkboxes
    function toggleButton() {
        const checkedCheckboxes = document.querySelectorAll('.bundle-checkbox:checked');
        deleteButton.disabled = checkedCheckboxes.length === 0; // Enable/disable button
        checkoutButton.disabled = checkedCheckboxes.length === 0; 
    }

    // Add event listeners to checkboxes
    const checkboxes = document.querySelectorAll('.bundle-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleButton);
    });
});


// Function to delete checked items
function deleteCheckedItems() {
    const checkedCheckboxes = document.querySelectorAll('.bundle-checkbox:checked');

    // Initialize itemIds as an empty array
    const itemIds = [];

    // Collect IDs of checked items
    checkedCheckboxes.forEach(checkbox => {
        const id = checkbox.closest('tr').dataset.id; // Ensure the ID is set in the row's data attribute
        itemIds.push(id); // Push the ID into the itemIds array
    });

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/cart/delete-selected'; // Your delete route

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = window.Laravel.csrfToken; // CSRF token

    form.appendChild(csrfInput);

    itemIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]'; // Array of IDs
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit(); // Submit the form
}

function proceedToCheckout() {
    window.location.href = "/checkout";
}

document.addEventListener('DOMContentLoaded', function() {
    updateButtonState(); // Initial state

    const checkboxes = document.querySelectorAll('.bundle-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateButtonState);
    });
});

function updateButtonState() {
    const checkedCheckboxes = document.querySelectorAll('.bundle-checkbox:checked');
    const checkoutButton = document.getElementById('checkout-button');

    // Disable the button if no items are checked, enable otherwise
    checkoutButton.disabled = checkedCheckboxes.length === 0;
}
