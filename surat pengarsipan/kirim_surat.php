<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    // Kirim dari draft
    if ($aksi === 'kirim' && isset($_POST['id_draft'])) {
        $id_draft = (int)$_POST['id_draft'];

        $stmt = $conn->prepare("SELECT * FROM draft_surat WHERE id = ? AND id_mahasiswa = ?");
        $stmt->bind_param("ii", $id_draft, $id_mahasiswa);
        $stmt->execute();
        $draft = $stmt->get_result()->fetch_assoc();

        if (!$draft) {
            echo "<script>alert('Draft tidak ditemukan.'); window.history.back();</script>";
            exit();
        }

        $template_id = $draft['template_id'];
        $file_surat = $draft['file_surat'];
        $lampiran = $draft['lampiran'] ?? null;

    } elseif ($aksi === 'kirim' || $aksi === 'draft') {
        $template_id = $_POST['template_id'] ?? null;

        if (!$template_id || !isset($_FILES['file_surat'])) {
            echo "<script>alert('Mohon lengkapi semua data.'); window.history.back();</script>";
            exit();
        }

        // Upload file surat
        $file = $_FILES['file_surat'];
        $allowed_ext = ['pdf', 'doc', 'docx'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>alert('Format file surat tidak didukung.'); window.history.back();</script>";
            exit();
        }

        $filename = uniqid('surat_') . '.' . $file_ext;
        $destination = '../uploads/file_surat/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            echo "<script>alert('Gagal mengunggah file surat.'); window.history.back();</script>";
            exit();
        }

        $file_surat = $filename;

        // Upload lampiran (optional)
        $lampiran = null;
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
            $lampiran_file = $_FILES['lampiran'];
            $lampiran_ext = strtolower(pathinfo($lampiran_file['name'], PATHINFO_EXTENSION));

            if (!in_array($lampiran_ext, $allowed_ext)) {
                echo "<script>alert('Format lampiran tidak didukung.'); window.history.back();</script>";
                exit();
            }

            $lampiran_name = uniqid('lampiran_') . '.' . $lampiran_ext;
            $lampiran_path = '../uploads/lampiran/' . $lampiran_name;

            if (!move_uploaded_file($lampiran_file['tmp_name'], $lampiran_path)) {
                echo "<script>alert('Gagal mengunggah lampiran.'); window.history.back();</script>";
                exit();
            }

            $lampiran = $lampiran_name;
        }

        // Jika simpan draft
        if ($aksi === 'draft') {
            $stmt = $conn->prepare("INSERT INTO draft_surat (id_mahasiswa, template_id, file_surat, lampiran, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiss", $id_mahasiswa, $template_id, $file_surat, $lampiran);
            $stmt->execute();

            echo "<script>alert('✅ Draft berhasil disimpan.'); window.location='draft.php';</script>";
            exit();
        }

    } else {
        echo "<script>alert('Aksi tidak dikenal.'); window.history.back();</script>";
        exit();
    }

    // Insert ke surat_masuk
    $id_staff_tu = 2;
    $id_pejabat_fakultas = 3;
    $status = 'dikirim';

    $stmt_insert = $conn->prepare("INSERT INTO surat_masuk (id_mahasiswa, id_staff_tu, id_pejabat_fakultas, template_id, file_surat, lampiran, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("iiiisss", $id_mahasiswa, $id_staff_tu, $id_pejabat_fakultas, $template_id, $file_surat, $lampiran, $status);

    if ($stmt_insert->execute()) {
        $id_surat = $stmt_insert->insert_id;

        // Log status
        $log_status = 'verifikasi';
        $stmt_log = $conn->prepare("INSERT INTO log_surat (id_surat, status, id_user) VALUES (?, ?, ?)");
        $stmt_log->bind_param("isi", $id_surat, $log_status, $id_mahasiswa);
        $stmt_log->execute();

        // Hapus draft jika kirim dari draft
        if (isset($id_draft)) {
            $hapus_draft = $conn->prepare("DELETE FROM draft_surat WHERE id = ?");
            $hapus_draft->bind_param("i", $id_draft);
            $hapus_draft->execute();
        }

        echo "<script>alert('✅ Surat berhasil dikirim ke Staff TU.'); window.location='draft.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal mengirim surat.'); window.history.back();</script>";
    }

} else {
    header("Location: buat_surat.php");
    exit();
}
