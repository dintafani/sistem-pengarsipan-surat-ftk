<?php
session_start();
include '../config/db.php';

// Cek role mahasiswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];

// Proses hapus draft
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];

    // Ambil file surat dan lampiran dari draft sebelum dihapus
    $query_file = $conn->prepare("SELECT file_surat, lampiran FROM draft_surat WHERE id = ? AND id_mahasiswa = ?");
    $query_file->bind_param("ii", $id_hapus, $id_mahasiswa);
    $query_file->execute();
    $result_file = $query_file->get_result();

    if ($result_file->num_rows > 0) {
        $row = $result_file->fetch_assoc();
        $file_path = '../uploads/file_surat/' . $row['file_surat'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        if (!empty($row['lampiran'])) {
            $lampiran_path = '../uploads/lampiran/' . $row['lampiran'];
            if (file_exists($lampiran_path)) {
                unlink($lampiran_path);
            }
        }

        $query_delete = $conn->prepare("DELETE FROM draft_surat WHERE id = ? AND id_mahasiswa = ?");
        $query_delete->bind_param("ii", $id_hapus, $id_mahasiswa);
        $query_delete->execute();
    }

    header("Location: draft.php");
    exit();
}

// Ambil draft milik mahasiswa, termasuk lampiran
$query = $conn->prepare("SELECT ds.id, ds.file_surat, ds.lampiran, ds.template_id, t.nama_template, ds.created_at 
                         FROM draft_surat ds 
                         JOIN templates t ON ds.template_id = t.id 
                         WHERE ds.id_mahasiswa = ? 
                         ORDER BY ds.created_at DESC");
$query->bind_param("i", $id_mahasiswa);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Draft Surat</title>
    <link rel="stylesheet" href="../assets/css/draft.css" />
</head>
<body>

<h2>Draft Surat Saya</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Nama Template</th>
                <th>File Surat</th>
                <th>Lampiran</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($draft = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($draft['nama_template']); ?></td>
                    <td>
                        <a href="../uploads/file_surat/<?= urlencode($draft['file_surat']); ?>" target="_blank">
                            <?= htmlspecialchars($draft['file_surat']); ?>
                        </a>
                    </td>
                    <td>
                        <?php if (!empty($draft['lampiran'])): ?>
                            <a href="../uploads/lampiran/<?= urlencode($draft['lampiran']); ?>" target="_blank">Lihat Lampiran</a>
                        <?php else: ?>
                            Tidak ada
                        <?php endif; ?>
                    </td>
                    <td><?= $draft['created_at']; ?></td>
                    <td>
                        <a class="button" href="../uploads/file_surat/<?= urlencode($draft['file_surat']); ?>" target="_blank">Lihat</a>

                        <form class="inline" action="kirim_surat.php" method="post" onsubmit="return confirm('Kirim surat ini ke Staff TU?');">
                            <input type="hidden" name="aksi" value="kirim" />
                            <input type="hidden" name="id_draft" value="<?= $draft['id']; ?>" />
                            <button type="submit" class="button">Kirim</button>
                        </form>

                        <a class="button" href="draft.php?hapus=<?= $draft['id']; ?>" onclick="return confirm('Yakin ingin menghapus draft ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada draft surat yang disimpan.</p>
<?php endif; ?>

<p><a href="buat_surat.php" class="button">Buat Surat Baru</a></p>

<p><a href="dashboard.php" class="button back">← Kembali</a></p>

<script src="../assets/js/draft.js" defer></script>
</body>
</html>
