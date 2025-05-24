<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pejabat_fakultas') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pejabat Fakultas</title>
    <link rel="stylesheet" href="../assets/css/dashboardmsw.css"> <!-- Gunakan CSS yang seragam -->
    <link rel="stylesheet" href="../assets/css/log_surat.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Pejabat Fakultas</h2>
            <p>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></p>

            <a href="surat_masuk.php" class="sidebar-btn">📥 Surat Masuk</a>
            <a href="arsip_surat.php" class="sidebar-btn">🗂️ Arsip Surat</a>

            <!-- Spacer -->
            <div style="height: 20px;"></div>

            <a href="../auth/logout.php" class="sidebar-btn">🚪 Logout</a>
        </aside>

        <main class="main-content">
            <h2>Dashboard Pejabat Fakultas</h2>

            <div class="log-box">
                <p>Gunakan menu di samping untuk meninjau dan menandatangani surat.</p>
                <a href="surat_masuk.php" class="log-btn">📋 Lihat Surat Masuk</a>
            </div>

            <h3>Petunjuk</h3>
            <ul class="info-list">
                <li>📌 Tinjau surat yang masuk dari TU.</li>
                <li>📌 Tandatangani atau tolak surat sesuai kebutuhan.</li>
                <li>📌 Arsipkan surat yang telah selesai ditindaklanjuti.</li>
            </ul>
        </main>
    </div>

    <script src="../assets/js/dashboardmsw.js"></script>
</body>
</html>

