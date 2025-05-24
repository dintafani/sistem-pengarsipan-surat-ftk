<?php
include '../config/db.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$password = $_POST['password']; // (opsional) bisa di-hash
$role = $_POST['role'];

// Validasi email unik
$cek = $conn->prepare("SELECT id FROM users WHERE email = ?");
$cek->bind_param("s", $email);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo "Email sudah terdaftar. <a href='register.php'>Kembali</a>";
    exit();
}

$sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nama, $email, $password, $role);

if ($stmt->execute()) {
    echo "Registrasi berhasil! <a href='login.php'>Login sekarang</a>";
} else {
    echo "Gagal menyimpan data.";
}
?>
