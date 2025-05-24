<?php 
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';

// Hapus template
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Ambil nama file
    $query = "SELECT file_path FROM templates WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    
    if ($stmt->fetch()) {
        $file = "../uploads/templates/" . $file_path;
        $stmt->close();

        // Kosongkan relasi foreign key di surat_masuk
        $reset = $conn->prepare("UPDATE surat_masuk SET template_id = NULL WHERE template_id = ?");
        $reset->bind_param("i", $id);
        $reset->execute();
        $reset->close();

        // Hapus file jika ada
        if (file_exists($file)) {
            unlink($file);
        }

        // Hapus data dari tabel templates
        $del = $conn->prepare("DELETE FROM templates WHERE id=?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();

        echo "<p style='color:green; text-align:center;'>✅ Template berhasil dihapus.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Gagal mengambil data template.</p>";
    }
}

$query = "SELECT * FROM templates";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Template Surat (Staff TU)</title>
    <link rel="stylesheet" href="../assets/css/templatesmsw.css" />
</head>
<body>

    <h3>Daftar Template Surat (Staff TU)</h3>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Nama Template</th>
                <th>Syarat</th>
                <th>Download</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_template']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['syarat'])) ?></td>
                <td>
                    <a class="download-link" href="../uploads/templates/<?= urlencode($row['file_path']) ?>" download>Unduh</a>
                </td>
                <td>
                    <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus template ini?')" style="color:#d9534f; font-weight:600;">Hapus</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="button-group">
        <a href="dashboard.php"><button>Kembali</button></a>
        <a href="upload_template.php"><button>Upload Template</button></a>
    </div>

    <script src="../assets/js/templatesmsw.js"></script>
</body>
</html>
