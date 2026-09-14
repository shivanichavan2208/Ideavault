/* =====================================================
   IDEAVAULT REGISTER JAVASCRIPT
   ===================================================== */


/* ---------- ELEMENTS ---------- */

const registerForm =
    document.getElementById("registerForm");

const nameInput =
    document.getElementById("name");

const emailInput =
    document.getElementById("email");

const passwordInput =
    document.getElementById("password");

const confirmPasswordInput =
    document.getElementById("confirmPassword");

const terms =
    document.getElementById("terms");


const nameError =
    document.getElementById("nameError");

const emailError =
    document.getElementById("emailError");

const passwordError =
    document.getElementById("passwordError");

const confirmError =
    document.getElementById("confirmError");

const registerMessage =
    document.getElementById("registerMessage");

const togglePassword =
    document.getElementById("togglePassword");


/* =====================================================
   SHOW / HIDE PASSWORD
   ===================================================== */

togglePassword.addEventListener("click", function () {

    if (passwordInput.type === "password") {

        passwordInput.type = "text";

        togglePassword.textContent = "🙈";

    } else {

        passwordInput.type = "password";

        togglePassword.textContent = "👁";

    }

});


/* =====================================================
   EMAIL VALIDATION
   ===================================================== */

function validateEmail(email) {

    const pattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return pattern.test(email);

}


/* =====================================================
   FORM SUBMIT
   ===================================================== */

registerForm.addEventListener(
    "submit",
    function (event) {

        event.preventDefault();


        /* CLEAR ERRORS */

        nameError.textContent = "";

        emailError.textContent = "";

        passwordError.textContent = "";

        confirmError.textContent = "";

        registerMessage.textContent = "";


        let valid = true;


        /* ---------- NAME ---------- */

        if (nameInput.value.trim() === "") {

            nameError.textContent =
                "Please enter your full name.";

            valid = false;

        } else if (
            nameInput.value.trim().length < 3
        ) {

            nameError.textContent =
                "Name must contain at least 3 characters.";

            valid = false;

        }


        /* ---------- EMAIL ---------- */

        if (emailInput.value.trim() === "") {

            emailError.textContent =
                "Please enter your email address.";

            valid = false;

        } else if (
            !validateEmail(emailInput.value.trim())
        ) {

            emailError.textContent =
                "Please enter a valid email address.";

            valid = false;

        }


        /* ---------- PASSWORD ---------- */

        if (passwordInput.value === "") {

            passwordError.textContent =
                "Please create a password.";

            valid = false;

        } else if (
            passwordInput.value.length < 6
        ) {

            passwordError.textContent =
                "Password must contain at least 6 characters.";

            valid = false;

        }


        /* ---------- CONFIRM PASSWORD ---------- */

        if (
            confirmPasswordInput.value === ""
        ) {

            confirmError.textContent =
                "Please confirm your password.";

            valid = false;

        } else if (
            confirmPasswordInput.value !==
            passwordInput.value
        ) {

            confirmError.textContent =
                "Passwords do not match.";

            valid = false;

        }


        /* ---------- TERMS ---------- */

        if (!terms.checked) {

            registerMessage.textContent =
                "Please agree to the Terms & Conditions.";

            registerMessage.style.color =
                "#ff9b9b";

            valid = false;

        }


        /* =================================================
           SUCCESS
           ================================================= */

        if (valid) {

            registerMessage.textContent =
                "Account created successfully! Redirecting...";

            registerMessage.style.color =
                "#58c4ff";


            /*
                FRONTEND DEMO ONLY

                Later this section will connect
                to PHP + MySQL for real registration.
            */

            setTimeout(function () {

                window.location.href =
                    "dashboard.html";

            }, 1500);

        }

    }
);