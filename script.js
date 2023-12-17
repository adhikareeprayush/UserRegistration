// Get references to password input fields and eye icons
let pass1eye = document.getElementById("pass1eye");
let pass2eye = document.getElementById("pass2eye");

let password1 = document.getElementById("password1");
let password2 = document.getElementById("password2");

// Event listener for keyup on password1 input
password1.addEventListener("keyup", function () {
    // Show or hide the eye icon based on whether password1 is empty
    if (password1.value != "") {
        pass1eye.style.display = "block";
    } else {
        pass1eye.style.display = "none";
    }
});

// Event listener for keyup on password2 input
password2.addEventListener("keyup", function () {
    // Show or hide the eye icon based on whether password2 is empty
    if (password2.value != "") {
        pass2eye.style.display = "block";
    } else {
        pass2eye.style.display = "none";
    }
});

// Event listener for click on pass1eye (eye icon for password1)
pass1eye.addEventListener("click", function () {
    // Toggle the visibility of password1 and update the eye icon
    if (password1.type === "password") {
        password1.type = "text";
        pass1eye.classList.remove("bi-eye");
        pass1eye.classList.add("bi-eye-slash");
    } else {
        password1.type = "password";
        pass1eye.classList.remove("bi-eye-slash");
        pass1eye.classList.add("bi-eye");
    }
});

// Event listener for click on pass2eye (eye icon for password2)
pass2eye.addEventListener("click", function () {
    // Toggle the visibility of password2
    if (password2.type === "password") {
        password2.type = "text";
    } else {
        password2.type = "password";
    }
});
