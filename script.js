/* =====================================================
   IDEAVAULT - JAVASCRIPT
   ===================================================== */


/* ================= NAVBAR SCROLL EFFECT ================= */

const navbar = document.querySelector("nav");

window.addEventListener("scroll", function () {

    if (window.scrollY > 30) {

        navbar.classList.add("scrolled");

    } else {

        navbar.classList.remove("scrolled");

    }

});


/* ================= SMOOTH SCROLL ================= */

const navLinks = document.querySelectorAll('a[href^="#"]');

navLinks.forEach(function (link) {

    link.addEventListener("click", function (event) {

        const targetId = this.getAttribute("href");

        if (targetId === "#") {
            return;
        }

        const target = document.querySelector(targetId);

        if (target) {

            event.preventDefault();

            target.scrollIntoView({
                behavior: "smooth"
            });

        }

    });

});


/* ================= PAGE LOAD ================= */

document.addEventListener("DOMContentLoaded", function () {

    console.log("IdeaVault loaded successfully!");

});