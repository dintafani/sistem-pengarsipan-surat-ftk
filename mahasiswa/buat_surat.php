<?php
include '../config/db.php';
session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM templates");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Surat</title>
</head>
<body>
    <h2>Buat Surat Baru</h2>
    <link rel="stylesheet" href="../assets/css/buat_surat.css">

    <form action="kirim_surat.php" method="POST" enctype="multipart/form-data">
        <label for="template_id">Pilih Template:</label><br>
        <select name="template_id" id="template_id" required>
            <option value="">-- Pilih Template --</option>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_template']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="file_surat">Upload Surat (PDF/DOCX):</label><br>
        <input type="file" name="file_surat" id="file_surat" accept=".pdf,.doc,.docx" required><br><br>

        <label for="lampiran">Upload Lampiran (opsional, PDF/DOCX):</label><br>
        <input type="file" name="lampiran" id="lampiran" accept=".pdf,.doc,.docx"><br><br>

        <button type="submit" name="aksi" value="draft">📝 Simpan Draft</button>
        <button type="submit" name="aksi" value="kirim">📤 Kirim ke Staff TU</button>
    </form>
    <br>
    <a href="draft.php"><button>📂 Lihat Draft Surat</button></a>
    <a href="dashboard.php"><button>Kembali</button></a>
    <script src="../assets/js/buat_surat.js"></script>

</body>
</html>
