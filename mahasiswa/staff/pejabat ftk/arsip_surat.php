<?php
session_start();
if ($_SESSION['role'] !== 'pejabat_fakultas') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';



// Ambil semua data arsip surat dan join dengan surat_final
$query = "
    SELECT arsip_surat.*, surat_final.tanda_tangan, surat_final.created_at 
    FROM arsip_surat 
    JOIN surat_final ON arsip_surat.id_surat_final = surat_final.id
    ORDER BY arsip_surat.id DESC
";

$result = $conn->query($query);
?>

<h3>Daftar Surat yang Telah Diarsipkan</h3>
<link rel="stylesheet" href="../assets/css/draft.css"> <!-- Gunakan CSS yang seragam -->
<table border="1" cellpadding="8">
    <tr>
        <th>File Surat</th>
        <th>Tanda Tangan</th>
        <th>Tanggal Arsip</th>
        <th>Aksi</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><a href="../uploads/surat_final/<?= htmlspecialchars($row['file_surat']) ?>" target="_blank">Lihat Surat</a></td>
                <td><?= htmlspecialchars($row['tanda_tangan']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Yakin ingin menghapus arsip surat ini?');">
                        <input type="hidden" name="hapus_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="hapus_arsip">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">Belum ada surat yang diarsipkan.</td></tr>
    <?php endif; ?>
</table>

<br>
<script src="../assets/js/draft.js"></script>

<a href="dashboard.php" class="button back">Kembali</a>




