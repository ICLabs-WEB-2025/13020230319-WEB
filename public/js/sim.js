document.addEventListener("DOMContentLoaded", function () {
    console.log("sim.js loaded");

    const sidebar = document.querySelector(".sidebar");
    const navbarToggler = document.querySelector(".navbar-toggler");

    if (navbarToggler && sidebar) {
        navbarToggler.addEventListener("click", function () {
            sidebar.classList.toggle("active");
        });
    }

    const createSimForm = document.getElementById("createSimForm");
    const editSimForm = document.getElementById("editSimForm");

    if (createSimForm) {
        createSimForm.addEventListener("submit", function (e) {
            const nama = document.getElementById("nama").value.trim();
            const nomorKtp = document.getElementById("nomor_ktp").value.trim();
            if (!nama || !nomorKtp) {
                e.preventDefault();
                alert("Silakan isi Nama Lengkap dan Nomor KTP!");
            }
        });
    }

    if (editSimForm) {
        editSimForm.addEventListener("submit", function (e) {
            const nama = document.getElementById("nama").value.trim();
            const nomorKtp = document.getElementById("nomor_ktp").value.trim();
            if (!nama || !nomorKtp) {
                e.preventDefault();
                alert("Silakan isi Nama Lengkap dan Nomor KTP!");
            }
        });
    }
});
