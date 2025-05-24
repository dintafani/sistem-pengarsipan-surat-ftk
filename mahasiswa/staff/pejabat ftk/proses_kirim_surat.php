<?php
include '../config/db.php';
session_start();
if ($_SESSION['role'] !== 'pejabat_fakultas') {
    header("Location: ../auth/login.php");
    exit();
}

$id_surat = (int)$_POST['id_surat'];
$id_pejabat = $_SESSION['user_id'];
$file = $_FILES['file_surat']['name'];
$tmp = $_FILES['file_surat']['tmp_name'];

// Simpan file
$folder = "../uploads/surat_final/";
move_uploaded_file($tmp, $folder . $file);

// Ambil ID staff TU
$getStaff = mysqli_query($conn, "SELECT id_staff_tu FROM surat_masuk WHERE id = $id_surat");
$staffData = mysqli_fetch_assoc($getStaff);
$id_staff = $staffData['id_staff_tu'] ?? 0;

// Validasi jika $id_staff null
if (!$id_staff) {
    echo "<script>alert('ID staff tidak ditemukan.'); window.location='surat_masuk.php';</script>";
    exit();
}

// Simpan ke surat_final
mysqli_query($conn, "INSERT INTO surat_final (id_staff_tu, id_pejabat_fakultas, file_surat, tanda_tangan, created_at, id_surat)
VALUES ($id_staff, $id_pejabat, '$file', 'ttd-digital', NOW(), $id_surat)");

// Update status surat_masuk
mysqli_query($conn, "UPDATE surat_masuk SET status='ditandatangani' WHERE id=$id_surat");

// Log surat
mysqli_query($conn, "INSERT INTO log_surat (id_surat, status, id_user, created_at)
VALUES ($id_surat, 'ditandatangani', $id_pejabat, NOW())");

echo "<script>alert('Surat berhasil ditandatangani dan dikirim ke Staff TU.'); window.location='surat_masuk.php';</script>";
