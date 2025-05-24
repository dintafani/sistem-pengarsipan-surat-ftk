<?php
include '../config/db.php';
session_start();

if ($_SESSION['role'] !== 'pejabat_fakultas') {
    header("Location: ../auth/login.php");
    exit();
}

// Proses upload tanda tangan (TTD)
if (isset($_POST['ttd_submit'])) {
    $id_surat = (int)$_POST['id_surat'];
    $id_pejabat = $_SESSION['user_id'];

    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['file_surat']['name']);
        $tmp = $_FILES['file_surat']['tmp_name'];

        $folder = "../uploads/surat_final/";
        $file_path = $folder . $file_name;

        if (move_uploaded_file($tmp, $file_path)) {
            // Ambil ID staff TU
            $getStaff = mysqli_query($conn, "SELECT id_staff_tu FROM surat_masuk WHERE id = $id_surat");
            $staffData = mysqli_fetch_assoc($getStaff);
            $id_staff = $staffData['id_staff_tu'] ?? 0;

            if (!$id_staff) {
                echo "<script>alert('ID staff tidak ditemukan.'); window.location='surat_masuk.php';</script>";
                exit();
            }

            // Insert ke surat_final
            mysqli_query($conn, "INSERT INTO surat_final (id_staff_tu, id_pejabat_fakultas, file_surat, tanda_tangan, created_at, id_surat)
                VALUES ($id_staff, $id_pejabat, '$file_name', 'ttd-digital', NOW(), $id_surat)");

            // Update status surat_masuk
            mysqli_query($conn, "UPDATE surat_masuk SET status='ditandatangani' WHERE id=$id_surat");

            // Log surat
            mysqli_query($conn, "INSERT INTO log_surat (id_surat, status, id_user, created_at)
                VALUES ($id_surat, 'ditandatangani', $id_pejabat, NOW())");

            echo "<script>alert('Surat berhasil ditandatangani dan dikirim ke Staff TU.'); window.location='surat_masuk.php';</script>";
            exit();
        } else {
            echo "<script>alert('Upload file gagal.'); window.location='surat_masuk.php';</script>";
            exit();
        }
    }
}

// Proses tolak surat
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($conn, "UPDATE surat_masuk SET status='ditolak' WHERE id=$id_hapus");
    mysqli_query($conn, "INSERT INTO log_surat (id_surat, status, id_user, created_at) 
        VALUES ($id_hapus, 'ditolak', {$_SESSION['user_id']}, NOW())");

    echo "<script>alert('Surat berhasil ditolak'); window.location.href='surat_masuk.php';</script>";
    exit();
}

// Ambil data surat dengan status 'disposisi'
$query = "SELECT sm.*, u.nama as nama_mahasiswa FROM surat_masuk sm 
          JOIN users u ON sm.id_mahasiswa = u.id
          WHERE sm.status = 'disposisi'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Surat Masuk Pejabat Fakultas</title>
</head>
<body>
<h2>Surat Masuk dari Staff TU</h2>
<link rel="stylesheet" href="../assets/css/draft.css"> <!-- Gunakan CSS yang seragam -->

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Mahasiswa</th>
        <th>Nomor Surat</th>
        <th>File</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
        <td><?= htmlspecialchars($row['nama_mahasiswa']); ?></td>
        <td><?= htmlspecialchars($row['nomor_surat']); ?></td>
        <td>
            <?php
            $file_path = '../uploads/file_disposisi/' . $row['file_surat'];
            if (file_exists($file_path)) {
                echo '<a href="' . $file_path . '" target="_blank">Lihat Surat</a>';
            } else {
                echo '<span style="color:red;">File tidak ditemukan</span>';
            }
            ?>
        </td>
        <td>
            <!-- Form upload tanda tangan -->
            <form method="POST" enctype="multipart/form-data" style="display:inline-block;">
                <input type="hidden" name="id_surat" value="<?= $row['id']; ?>">
                <input type="file" name="file_surat" required>
                <button type="submit" name="ttd_submit">Tandatangani</button>
            </form>
            &nbsp;|&nbsp;
            <a href="?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin tolak surat ini?')">Tolak</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<br>
<!-- Tombol Kembali ke Dashboard -->
     <script src="../assets/js/draft.js"></script>

    <a href="dashboard.php"class="button back">Kembali</a>

</body>
</html>
