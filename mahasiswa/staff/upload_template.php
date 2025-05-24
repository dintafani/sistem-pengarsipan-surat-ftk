<?php
session_start();
if ($_SESSION['role'] !== 'staff_tu') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';

$uploadMessage = '';
$uploadClass = '';

if (isset($_POST['upload'])) {
    $nama_template = $_POST['nama_template'];
    $syarat = $_POST['syarat'];
    $file_name = $_FILES['template']['name'];
    $file_tmp = $_FILES['template']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx'];

    if (!in_array($file_ext, $allowed)) {
        $uploadMessage = "❌ Hanya file PDF, DOC, atau DOCX yang diizinkan.";
        $uploadClass = "error";
    } else {
        $folder = "../uploads/templates/";
        if (move_uploaded_file($file_tmp, $folder . $file_name)) {
            $query = "INSERT INTO templates (nama_template, file_path, syarat) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $nama_template, $file_name, $syarat);
            $stmt->execute();

            $uploadMessage = "✅ Template berhasil diunggah.";
            $uploadClass = "success";
        } else {
            $uploadMessage = "❌ Gagal mengunggah file.";
            $uploadClass = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Upload Template Surat - Staff TU</title>
    <link rel="stylesheet" href="../assets/css/buat_surat.css" />
    <style>
        /* Tambahan styling khusus untuk halaman ini */
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], textarea, input[type="file"], select {
            width: 100%;
            max-width: 500px;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
        }
        button {
            margin-right: 10px;
            padding: 8px 16px;
            cursor: pointer;
        }
        .message {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .btn-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Upload Template Surat</h2>

    <?php if ($uploadMessage): ?>
        <p class="message <?= $uploadClass ?>"><?= htmlspecialchars($uploadMessage) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_template">Nama Template:</label>
            <input type="text" id="nama_template" name="nama_template" required />
        </div>

        <div class="form-group">
            <label for="syarat">Syarat:</label>
            <textarea id="syarat" name="syarat" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="template">File Template (.pdf, .doc, .docx):</label>
            <input type="file" id="template" name="template" accept=".pdf,.doc,.docx" required />
        </div>

        <button type="submit" name="upload">Upload Template</button>
    </form>

    <div class="btn-group">
        <a href="daftar_template.php"><button type="button">Daftar Template</button></a>
        <a href="dashboard.php"><button type="button">Kembali</button></a>
    </div>
</body>
</html>
