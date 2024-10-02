// Handle checkbox selection for both containers
document.querySelectorAll('.bundle-checkbox, .small-screen-bundle-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const bundleName = this.getAttribute('data-bundle');
        const checkboxes = document.querySelectorAll(`input[data-bundle="${bundleName}"], .small-screen-bundle-checkbox[data-bundle="${bundleName}"]`);

        checkboxes.forEach(cb => {
            cb.checked = this.checked; // Check/uncheck all checkboxes with the same bundle
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const deleteButton = document.getElementById('delete-button');
    const deleteButtonSmall = document.getElementById('delete-button-small'); // For small screen
    // const checkoutButton = document.getElementById('checkout-button');
    // const checkoutButtonSmall = document.getElementById('checkout-button-small'); // For small screen

    // Function to toggle the delete and checkout buttons based on checked checkboxes
    function toggleButton() {
        const checkedCheckboxes = document.querySelectorAll('.bundle-checkbox:checked, .small-screen-bundle-checkbox:checked');
        const isDisabled = checkedCheckboxes.length === 0; // Check if no items are selected

        deleteButton.disabled = isDisabled; // Enable/disable delete button
        deleteButtonSmall.disabled = isDisabled; // Enable/disable small screen delete button
        checkoutButton.disabled = isDisabled; // Enable/disable checkout button
        checkoutButtonSmall.disabled = isDisabled; // Enable/disable small screen checkout button
    }

    // Add event listeners to checkboxes for both containers
    const checkboxes = document.querySelectorAll('.bundle-checkbox, .small-screen-bundle-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleButton);
    });

    // Initial state check
    toggleButton();
});

// Function to delete checked items
function deleteCheckedItems() {
    const checkedCheckboxes = document.querySelectorAll('.bundle-checkbox:checked');

    // Initialize itemIds as an empty array
    const itemIds = [];

    // Collect IDs of checked items
    checkedCheckboxes.forEach(checkbox => {
        const id = checkbox.closest('tr') ? checkbox.closest('tr').dataset.id : checkbox.closest('.small-screen-cart-card').dataset.id; // Adjust for small screen
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

function deleteCheckedItemsSmallScreen() {
    const checkedCheckboxes = document.querySelectorAll('.small-screen-bundle-checkbox:checked');

    // Initialize itemIds as an empty array
    const itemIds = [];

    // Collect IDs of checked items
    checkedCheckboxes.forEach(checkbox => {
        const id = checkbox.closest('.small-screen-cart-card').dataset.id; // Get ID from the card's data attribute
        itemIds.push(id); // Push the ID into the itemIds array
    });

    if (itemIds.length === 0) {
        return; // Exit if no items are selected
    }

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

// Function to redirect to the checkout page
function proceedToCheckout() {
    window.location.href = "/checkout";
}

// Function to update the button state
function updateButtonState() {
    const checkedCheckboxes = document.querySelectorAll('.bundle-checkbox:checked, .small-screen-bundle-checkbox:checked');
    // const checkoutButton = document.getElementById('checkout-button');
    // const checkoutButtonSmall = document.getElementById('checkout-button-small'); // For small screen

    // Disable the button if no items are checked, enable otherwise
    const isDisabled = checkedCheckboxes.length === 0;
    checkoutButton.disabled = isDisabled;
    checkoutButtonSmall.disabled = isDisabled; // For small screen
}

document.addEventListener('DOMContentLoaded', updateButtonState);