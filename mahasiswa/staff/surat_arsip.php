<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

$query = "SELECT surat_masuk.*, users.nama AS nama_mahasiswa FROM surat_masuk 
          JOIN users ON surat_masuk.id_mahasiswa = users.id 
          WHERE surat_masuk.status = 'diarsipkan'";
$result = $conn->query($query);
?>

<h2>Daftar Surat Arsip</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Surat</th>
        <th>Mahasiswa</th>
        <th>File Surat</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
        <td><a href="../uploads/file_surat/<?= htmlspecialchars($row['file_surat']) ?>" target="_blank">Lihat</a></td>
        <td><?= $row['status'] ?></td>
        <td>-</td>
    </tr>
    <?php endwhile; ?>
</table>
