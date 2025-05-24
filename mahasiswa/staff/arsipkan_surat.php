<?php
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

// === PROSES HAPUS ARSIP ===
if (isset($_POST['hapus_arsip'])) {
    $id_hapus = $_POST['hapus_id'];

    $stmt = $conn->prepare("DELETE FROM arsip_surat WHERE id = ?");
    $stmt->bind_param("i", $id_hapus);

    if ($stmt->execute()) {
        $pesan = ["type" => "warning", "text" => "Arsip surat berhasil dihapus."];
    } else {
        $pesan = ["type" => "error", "text" => "Gagal menghapus arsip surat."];
    }
}

// Ambil semua data arsip dan surat_final terkait
$query = "
    SELECT arsip_surat.*, surat_final.tanda_tangan, surat_final.created_at 
    FROM arsip_surat 
    JOIN surat_final ON arsip_surat.id_surat_final = surat_final.id
    ORDER BY arsip_surat.id DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arsip Surat</title>
    <link rel="stylesheet" href="../assets/css/log_surat.css">
</head>
<body>
<div class="container">
    <h2>Daftar Surat yang Telah Diarsipkan</h2>

    <?php if (isset($pesan)): ?>
        <div class="alert <?= $pesan['type'] ?>">
            <?= htmlspecialchars($pesan['text']) ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>File Surat</th>
                <th>Tanda Tangan</th>
                <th>Tanggal Arsip</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="../uploads/surat_final/<?= htmlspecialchars($row['file_surat']) ?>" target="_blank">
                                Lihat Surat
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['tanda_tangan']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Yakin ingin menghapus arsip surat ini?');" style="display:inline;">
                                <input type="hidden" name="hapus_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="hapus_arsip">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;">Belum ada surat yang diarsipkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="dashboard.php"><button>Kembali</button></a>
</div>

<script src="../assets/js/log_surat.js"></script>
</body>
</html>
