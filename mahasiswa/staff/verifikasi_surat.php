<?php
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';

$id_surat = $_GET['id'] ?? null;
if (!$id_surat) {
    die("ID surat tidak ditemukan.");
}

// Ambil data surat
$stmt = $conn->prepare("SELECT * FROM surat_masuk WHERE id = ?");
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$result = $stmt->get_result();
$surat = $result->fetch_assoc();

if (!$surat) {
    die("Surat tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_surat = $_POST['nomor_surat'];
    $status_baru = 'disposisi';  // Ubah status jadi disposisi langsung
    $cap_surat = $_POST['cap_surat'] ?? 'disetujui';

    // Proses upload file surat baru (opsional)
    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/file_disposisi/';
        $filename = time() . '_' . basename($_FILES['file_surat']['name']);
        $target_file = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['file_surat']['tmp_name'], $target_file)) {
            die("Gagal mengupload file surat.");
        }

        // Simpan hanya nama file untuk database (tanpa ../uploads)
        $filename_for_db = $filename;
    } else {
        // Pakai file lama dari mahasiswa (sudah ada di DB)
        $filename_for_db = $surat['file_surat'];
    }

    // Update surat dengan status disposisi
    $update = $conn->prepare("UPDATE surat_masuk SET nomor_surat = ?, status = ?, cap_surat = ?, file_surat = ? WHERE id = ?");
    $update->bind_param("ssssi", $nomor_surat, $status_baru, $cap_surat, $filename_for_db, $id_surat);

    if ($update->execute()) {
        // Insert log disposisi
        $log = $conn->prepare("INSERT INTO log_surat (id_surat, status, id_user) VALUES (?, ?, ?)");
        $log->bind_param("isi", $id_surat, $status_baru, $_SESSION['user_id']);
        $log->execute();

        echo "<script>alert('Surat berhasil diverifikasi dan didisposisi ke pejabat.'); window.location='surat_masuk.php';</script>";
        exit();
    } else {
        echo "Gagal update surat.";
    }
}
?>

<h2>Verifikasi Surat</h2>
 <link rel="stylesheet" href="../assets/css/verifikasi.css" />

<form method="POST" action="" enctype="multipart/form-data">
    <label>Nomor Surat:</label><br>
    <input type="text" name="nomor_surat" required><br><br>

    <label>Cap Surat:</label><br>
    <input type="text" name="cap_surat" value="disetujui"><br><br>

    <label>Upload File Surat (opsional):</label><br>
    <input type="file" name="file_surat" accept=".pdf,.doc,.docx"><br><br>

    <button type="submit">Simpan Verifikasi dan Disposisi</button>
</form>
    <script src="../assets/js/verifikasi.js"></script>

<p><a href="surat_masuk.php">Kembali ke daftar surat</a></p>