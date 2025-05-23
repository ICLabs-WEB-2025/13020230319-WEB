document.addEventListener("DOMContentLoaded", function () {
    console.log("dashboard.js loaded");

    const sidebar = document.querySelector(".sidebar");
    const navbarToggler = document.querySelector(".navbar-toggler");

    navbarToggler.addEventListener("click", function () {
        sidebar.classList.toggle("active");
    });

    const filterForm = document.getElementById("filterForm");
    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            const query = document
                .querySelector('input[name="query"]')
                .value.trim();
            const filterType = document.getElementById("filter_type").value;
            const filter = document.getElementById("filter").value;
            if (!query && !filterType && !filter) {
                e.preventDefault();
                alert("Silakan isi salah satu filter atau kolom pencarian!");
            }
        });
    }
});
