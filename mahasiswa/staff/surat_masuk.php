<?php
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';

// Query ambil surat masuk yang statusnya dikirim, plus nama mahasiswa
$query = "SELECT surat_masuk.*, users.nama AS nama_mahasiswa 
          FROM surat_masuk 
          JOIN users ON surat_masuk.id_mahasiswa = users.id 
          WHERE surat_masuk.status = 'dikirim'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Surat Masuk dari Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/log_surat.css" />
</head>
<body>

<h3>Surat Masuk dari Mahasiswa</h3>

<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th>ID Surat</th>
            <th>Mahasiswa</th>
            <th>File Surat</th>
            <th>Lampiran</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
            <td>
                <a href="../uploads/file_surat/<?= htmlspecialchars($row['file_surat']) ?>" target="_blank" class="download-link">Lihat</a>
            </td>
            <td>
                <?php if (!empty($row['lampiran'])): ?>
                    <a href="../uploads/lampiran/<?= htmlspecialchars($row['lampiran']) ?>" target="_blank" class="download-link">Lihat Lampiran</a>
                <?php else: ?>
                    Tidak ada
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <a href="verifikasi_surat.php?id=<?= $row['id'] ?>" class="action-link">Verifikasi</a> | 
                <a href="tolak_surat.php?id=<?= $row['id'] ?>" class="action-link danger" onclick="return confirm('Yakin ingin menolak surat ini?');">Tolak</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align:center;">Tidak ada surat masuk yang perlu diverifikasi.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<br>

<div class="button-group">
    <a href="dashboard.php"><button>Kembali</button></a>
</div>

<script src="../assets/js/log_surat.js"></script>

</body>
</html>
