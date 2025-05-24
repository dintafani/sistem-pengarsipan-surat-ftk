<?php
session_start();
include '../config/db.php';

// Pastikan user sudah login dan role mahasiswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];

// Proses hapus surat (tampilkan surat dihapus dari list, tapi tidak menghapus data asli)
if (isset($_POST['hapus_surat'])) {
    $id_surat_final = $_POST['hapus_surat_id'];

    $stmt = $conn->prepare("INSERT INTO surat_final_dihapus (id_mahasiswa, id_surat_final, deleted_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $id_mahasiswa, $id_surat_final);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil surat final yang sudah dikirim dan status 'diarsipkan' untuk mahasiswa ini
$query = "
    SELECT sf.id, sf.file_surat 
    FROM surat_final sf
    JOIN log_surat ls ON sf.id_surat = ls.id_surat
    JOIN surat_masuk sm ON sf.id_surat = sm.id
    WHERE sm.id_mahasiswa = ? 
      AND ls.status = 'diarsipkan'
      AND sf.id NOT IN (
        SELECT id_surat_final FROM surat_final_dihapus WHERE id_mahasiswa = ?
      )
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_mahasiswa, $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>Surat Balasan dari Fakultas</h3>
<link rel="stylesheet" href="../assets/css/log_surat.css">

<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
    <tr>
        <th>File Surat</th>
        <th>Aksi</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['file_surat']) ?></td>
                <td>
                    <a href="../uploads/surat_final/<?= htmlspecialchars($row['file_surat']) ?>" download>Unduh</a>

                    <form method="post" onsubmit="return confirm('Yakin ingin menghapus surat ini dari daftar?');" style="display:inline-block; margin-left:10px;">
                        <input type="hidden" name="hapus_surat_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="hapus_surat">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">Belum ada surat balasan dari fakultas.</td>
        </tr>
    <?php endif; ?>
</table>

<br>
<script src="../assets/js/log_surat.js"></script>
<a href="dashboard.php"><button>Kembali</button></a>

