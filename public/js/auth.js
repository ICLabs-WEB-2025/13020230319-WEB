document.addEventListener("DOMContentLoaded", function () {
    console.log("auth.js loaded");

    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);
            this.querySelector("i").classList.toggle("fa-eye");
            this.querySelector("i").classList.toggle("fa-eye-slash");
        });
    }

    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            if (!email || !password) {
                e.preventDefault();
                alert("Silakan isi email dan password!");
            }
        });
    }
});
