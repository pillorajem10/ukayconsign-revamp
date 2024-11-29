function verifyPassword() {
    // Show the custom modal for password entry
    document.getElementById('passwordModal').style.display = 'block';
}

function submitForm() {
    var password = document.getElementById('password').value;

    // Check if password is correct
    if (password === "850625") {
        // Submit the form if the password is correct
        document.getElementById('passwordForm').submit();
    } else {
        // Show an alert if the password is incorrect
        alert("Incorrect password. Action canceled.");
    }
}

function closeModal() {
    // Close the modal if the user cancels
    document.getElementById('passwordModal').style.display = 'none';
}