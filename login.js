/* =====================================================
   IDEAVAULT LOGIN JAVASCRIPT
   ===================================================== */


/* ---------- ELEMENTS ---------- */

const loginForm = document.getElementById("loginForm");

const email = document.getElementById("email");
const password = document.getElementById("password");

const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");

const loginMessage = document.getElementById("loginMessage");

const togglePassword = document.getElementById("togglePassword");

const forgotPassword = document.getElementById("forgotPassword");


/* =====================================================
   SHOW / HIDE PASSWORD
   ===================================================== */

togglePassword.addEventListener("click", function () {

    if (password.type === "password") {

        password.type = "text";

        togglePassword.textContent = "🙈";

    } else {

        password.type = "password";

        togglePassword.textContent = "👁";

    }

});


/* =====================================================
   EMAIL VALIDATION
   ===================================================== */

function validateEmail(value) {

    const emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return emailPattern.test(value);

}


/* =====================================================
   LOGIN FORM
   ===================================================== */

loginForm.addEventListener("submit", function (event) {

    event.preventDefault();

    /* Clear previous messages */

    emailError.textContent = "";
    passwordError.textContent = "";
    loginMessage.textContent = "";


    let valid = true;


    /* ---------- EMAIL ---------- */

    if (email.value.trim() === "") {

        emailError.textContent =
            "Please enter your email address.";

        valid = false;

    } else if (!validateEmail(email.value.trim())) {

        emailError.textContent =
            "Please enter a valid email address.";

        valid = false;

    }


    /* ---------- PASSWORD ---------- */

    if (password.value.trim() === "") {

        passwordError.textContent =
            "Please enter your password.";

        valid = false;

    } else if (password.value.length < 6) {

        passwordError.textContent =
            "Password must contain at least 6 characters.";

        valid = false;

    }


    /* ---------- RESULT ---------- */

    if (valid) {

        loginMessage.textContent =
            "Login successful! Redirecting...";

        loginMessage.style.color = "#58c4ff";


        /*
           Temporary front-end redirect.

           Later, when PHP/database is added,
           this will be replaced by real authentication.
        */

        setTimeout(function () {

            window.location.href = "index.html";

        }, 1200);

    }

});


/* =====================================================
   FORGOT PASSWORD
   ===================================================== */

forgotPassword.addEventListener("click", function (event) {

    event.preventDefault();

    loginMessage.textContent =
        "Password reset feature will be available soon.";

    loginMessage.style.color = "#58c4ff";

});