<?php
include '../config/db.php';
session_start();
if ($_SESSION['role'] !== 'pejabat_fakultas') {
    header("Location: ../auth/login.php");
    exit();
}

$id_surat = $_GET['id'] ?? 0;
?>

<h2>Upload Surat Bertanda Tangan</h2>
<form action="proses_kirim_surat.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_surat" value="<?= $id_surat ?>">
    <label>Upload Surat Bertanda Tangan:</label><br>
    <input type="file" name="file_surat" required><br><br>
    <button type="submit">Kirim ke Staff TU</button>
</form>
