// Function to toggle edit mode for SPR
function toggleEdit(id) {
    // Get the elements by their respective IDs
    let textSpan = document.getElementById(`spr-text-${id}`);
    let inputField = document.getElementById(`spr-input-${id}`);
    let form = document.getElementById(`spr-form-${id}`);
    let editButton = document.getElementById(`edit-spr-btn-${id}`);
    
    // Check if the form is hidden or not
    if (form.style.display === "none") {
        // Show the form and hide the text
        form.style.display = "inline-block";
        textSpan.style.display = "none";
        editButton.innerHTML = "Cancel"; // Change button text to "Cancel"
    } else {
        // Hide the form and show the text
        form.style.display = "none";
        textSpan.style.display = "inline";
        editButton.innerHTML = "Edit Retail Price"; // Change button text back to "Edit"
    }
}
