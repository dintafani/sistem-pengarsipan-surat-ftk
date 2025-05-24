<?php
session_start();
include '../config/db.php';

// Cek login dan role mahasiswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'] ?? 0;

// Proses soft delete log status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_log'])) {
    $id_log = (int)$_POST['id_log'];
    // Insert ke tabel log_surat_dihapus
    $stmt = $conn->prepare("INSERT INTO log_surat_dihapus (id_mahasiswa, id_log_surat, deleted_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $id_mahasiswa, $id_log);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('✅ Status berhasil dihapus.'); window.location='".$_SERVER['PHP_SELF']."';</script>";
    exit;
}

// Ambil status log, kecuali yang sudah dihapus
$query = "
    SELECT ls.id, sm.nomor_surat, ls.status, ls.created_at 
    FROM log_surat ls
    JOIN surat_masuk sm ON sm.id = ls.id_surat
    WHERE sm.id_mahasiswa = ?
      AND ls.id NOT IN (
        SELECT id_log_surat FROM log_surat_dihapus WHERE id_mahasiswa = ?
      )
    ORDER BY ls.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_mahasiswa, $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Log Status Surat</title>
    <link rel="stylesheet" href="../assets/css/log_surat.css" />
</head>
<body>

    <h2>Status Surat Anda</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nomor Surat</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="Nomor Surat"><?= htmlspecialchars($row['nomor_surat']) ?: 'Belum Ada'; ?></td>
                    <td data-label="Status"><?= htmlspecialchars($row['status']); ?></td>
                    <td data-label="Waktu"><?= htmlspecialchars($row['created_at']); ?></td>
                    <td data-label="Aksi">
                        <form method="post" onsubmit="return confirm('Yakin ingin menghapus status ini?');" style="display:inline-block;">
                            <input type="hidden" name="id_log" value="<?= $row['id']; ?>">
                            <button type="submit" name="hapus_log">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; margin-top: 40px;">Belum ada status surat yang tersedia.</p>
    <?php endif; ?>

    <div style="text-align:center; margin-top: 30px;">
        <a href="dashboard.php"><button>Kembali</button></a>
    </div>

<script src="../assets/js/log_surat.js" defer></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
