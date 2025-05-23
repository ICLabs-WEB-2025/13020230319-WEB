document.addEventListener("DOMContentLoaded", function () {
    console.log("custom.js loaded");
    const form = document.getElementById("searchForm");
    if (form) {
        form.addEventListener("submit", function (e) {
            const simNumber = document
                .getElementById("sim_number")
                .value.trim();
            const birthDate = document.getElementById("birth_date").value;
            if (!simNumber || !birthDate) {
                e.preventDefault();
                alert("Silakan isi Nomor SIM dan Tanggal Lahir!");
            }
        });
    }
});
