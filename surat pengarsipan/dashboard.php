\<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

$id_mahasiswa = $_SESSION['user_id'] ?? 0;

$result = mysqli_query($conn, "
    SELECT ls.id, sm.nomor_surat, ls.status, ls.created_at 
    FROM log_surat ls
    JOIN surat_masuk sm ON sm.id = ls.id_surat
    WHERE sm.id_mahasiswa = $id_mahasiswa
      AND ls.id NOT IN (
        SELECT id_log_surat FROM log_surat_dihapus WHERE id_mahasiswa = $id_mahasiswa
      )
    ORDER BY ls.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/dashboardmsw.css">
    <link rel="stylesheet" href="../assets/css/log_surat.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Mahasiswa</h2>
            <p>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></p>

            <a href="templates.php" class="sidebar-btn">📄 Template Surat</a>
            <a href="buat_surat.php" class="sidebar-btn">✉️ Kirim Surat</a>
            <a href="surat_final.php" class="sidebar-btn">📥 Surat Masuk</a>

            <!-- Spacer sebagai pemisah -->
            <div style="height: 20px;"></div>

            <a href="../auth/logout.php" class="sidebar-btn">🚪 Logout</a>
        </aside>

        <main class="main-content">
            <h2>Status Surat</h2>
            <div class="log-box">
                <p>Lihat perkembangan surat yang kamu kirim di sini.</p>
                <a href="log_surat.php" class="log-btn">📊 Lihat Log Surat Lengkap</a>
            </div>

            <h3>Log Surat Terbaru</h3>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nomor_surat'] ?: 'Belum Ada'); ?></td>
                                <td><?= htmlspecialchars($row['status']); ?></td>
                                <td><?= htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center;">Belum ada log surat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script src="../assets/js/dashboardmsw.js"></script>
</body>
</html>
