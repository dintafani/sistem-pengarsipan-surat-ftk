document.getElementById("loginForm").addEventListener("submit", function(event) {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (email === "" || password === "") {
        alert("Email dan Password wajib diisi!");
        event.preventDefault(); // Mencegah form terkirim jika kosong
    }
});
