let pass1eye = document.getElementById("pass1eye");
let pass2eye = document.getElementById("pass2eye");

let password1 = document.getElementById("password1");
let password2 = document.getElementById("password2");


//if password1 is not empty, show the eye icon
password1.addEventListener("keyup", function () {
    if (password1.value != "") {
        pass1eye.style.display = "block";
    } else {
        pass1eye.style.display = "none";
    }
});

//if password2 is not empty, show the eye icon
password2.addEventListener("keyup", function () {
    if (password2.value != "") {
        pass2eye.style.display = "block";
    } else {
        pass2eye.style.display = "none";
    }
});


pass1eye.addEventListener("click", function () {
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

pass2eye.addEventListener("click", function () {
    if (password2.type === "password") {
        password2.type = "text";
    } else {
        password2.type = "password";
    }
});

