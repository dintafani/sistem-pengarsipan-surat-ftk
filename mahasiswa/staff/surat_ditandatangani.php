<?php 
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

// === PROSES KIRIM KE MAHASISWA ===
if (isset($_POST['kirim'])) {
    $id_surat = $_POST['id_surat'];
    $id_user = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO log_surat (id_surat, status, id_user) VALUES (?, 'diarsipkan', ?)");
    $stmt->bind_param("ii", $id_surat, $id_user);

    if ($stmt->execute()) {
        $stmt2 = $conn->prepare("UPDATE surat_masuk SET status = 'dikirim' WHERE id = ?");
        $stmt2->bind_param("i", $id_surat);
        $stmt2->execute();

        $pesan = ["type" => "success", "text" => "Surat berhasil dikirim ke mahasiswa."];
    } else {
        $pesan = ["type" => "error", "text" => "Gagal mengirim surat. Pastikan ID surat valid."];
    }
}

// === PROSES ARSIPKAN ===
if (isset($_POST['arsip'])) {
    $id_final = $_POST['id_final'];
    $file = $_POST['file'];

    $stmt = $conn->prepare("INSERT INTO arsip_surat (id_surat_final, file_surat) VALUES (?, ?)");
    $stmt->bind_param("is", $id_final, $file);

    if ($stmt->execute()) {
        $pesan = ["type" => "info", "text" => "Surat berhasil diarsipkan."];
    } else {
        $pesan = ["type" => "error", "text" => "Gagal mengarsipkan surat."];
    }
}

// === PROSES HAPUS (Soft Delete) ===
if (isset($_POST['hapus_final'])) {
    $id_hapus = $_POST['hapus_id'];

    $stmt = $conn->prepare("UPDATE surat_final SET is_deleted_staff = 1 WHERE id = ?");
    $stmt->bind_param("i", $id_hapus);

    if ($stmt->execute()) {
        $pesan = ["type" => "warning", "text" => "Surat dihapus dari tampilan staff."];
    } else {
        $pesan = ["type" => "error", "text" => "Gagal menghapus surat."];
    }
}

// Ambil data surat final yang belum dihapus
$result = $conn->query("SELECT * FROM surat_final WHERE is_deleted_staff = 0");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Ditandatangani Pejabat</title>
    <link rel="stylesheet" href="../assets/css/log_surat.css">
</head>
<body>
<div class="container">
    <h2>Surat Ditandatangani Pejabat</h2>

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
                        <td>
                            <!-- Kirim -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_surat" value="<?= $row['id_surat'] ?>">
                                <button type="submit" name="kirim">Kirim</button>
                            </form>

                            <!-- Arsipkan -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_final" value="<?= $row['id'] ?>">
                                <input type="hidden" name="file" value="<?= htmlspecialchars($row['file_surat']) ?>">
                                <button type="submit" name="arsip">Arsipkan</button>
                            </form>

                            <!-- Hapus -->
                            <form method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                <input type="hidden" name="hapus_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="hapus_final">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada surat final yang tersedia.</td>
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
