<?php
session_start();
include '../config/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifikasi password (tanpa hash, bisa ditambahkan hash kalau mau)
    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // Redirect sesuai role
        if ($user['role'] === 'mahasiswa') {
            header("Location: ../mahasiswa/dashboard.php");
        } elseif ($user['role'] === 'staff_tu') {
            header("Location: ../staff/dashboard.php");
        } elseif ($user['role'] === 'pejabat_fakultas') {
            header("Location: ../pejabat/dashboard.php");
        }
        exit();
    } else {
        echo "Password salah!";
    }
} else {
    echo "Pengguna tidak ditemukan!";
}
?>
