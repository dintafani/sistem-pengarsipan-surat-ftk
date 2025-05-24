<?php
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';

$id_surat = $_GET['id'] ?? null;
if (!$id_surat) {
    die("ID surat tidak ditemukan.");
}

$status_baru = 'ditolak';

$update = $conn->prepare("UPDATE surat_masuk SET status = ? WHERE id = ?");
$update->bind_param("si", $status_baru, $id_surat);

if ($update->execute()) {
    $log = $conn->prepare("INSERT INTO log_surat (id_surat, status, id_user) VALUES (?, ?, ?)");
    $log->bind_param("isi", $id_surat, $status_baru, $_SESSION['user_id']);
    $log->execute();

    echo "<script>alert('Surat berhasil ditolak.'); window.location='surat_masuk.php';</script>";
    exit();
} else {
    echo "Gagal menolak surat.";
}
?>
