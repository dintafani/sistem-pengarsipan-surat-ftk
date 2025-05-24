<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Staff TU</title>
    <link rel="stylesheet" href="../assets/css/dashboardmsw.css"> <!-- Gunakan CSS yang seragam -->
    <link rel="stylesheet" href="../assets/css/log_surat.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Staff TU</h2>
            <p>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></p>

            <a href="upload_template.php" class="sidebar-btn">📤 Upload Template</a>
            <a href="daftar_template.php" class="sidebar-btn">📁 Daftar Template</a>
            <a href="surat_masuk.php" class="sidebar-btn">📥 Surat Masuk</a>
            <a href="surat_ditandatangani.php" class="sidebar-btn">📋 Surat Disposisi</a>
            <a href="arsipkan_surat.php" class="sidebar-btn">🗂️ Arsip</a>

            <!-- Spacer -->
            <div style="height: 20px;"></div>

            <a href="../auth/logout.php" class="sidebar-btn">🚪 Logout</a>
        </aside>

        <main class="main-content">
            <h2>Dashboard Staff TU</h2>

            <div class="log-box">
                <p>Gunakan menu di samping untuk mengelola surat dan template surat.</p>
                <a href="surat_masuk.php" class="log-btn">📥 Lihat Semua Surat Masuk</a>
            </div>

            <h3>Informasi Umum</h3>
            <ul class="info-list">
                <li>📌 Upload template baru untuk mahasiswa.</li>
                <li>📌 Proses surat masuk dan lakukan disposisi.</li>
                <li>📌 Arsipkan surat yang telah selesai.</li>
            </ul>
        </main>
    </div>

    <script src="../assets/js/dashboardmsw.js"></script>
</body>
</html>
