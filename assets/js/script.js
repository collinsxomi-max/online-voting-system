function confirmVote() {
    return confirm("Are you sure you want to cast your vote?");
}

function validateRegistration() {
    const regNo = document.getElementById("reg_no").value.trim();
    const name = document.getElementById("name").value.trim();
    const emailField = document.getElementById("email");
    const email = emailField ? emailField.value.trim() : "";
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    if (!regNo || !name || !password || !confirmPassword) {
        alert("All fields are required.");
        return false;
    }

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Enter a valid email address.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
    }

    return true;
}
