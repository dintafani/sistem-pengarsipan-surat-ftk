<?php
include '../config/db.php';

$query = "SELECT * FROM templates";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Template Surat</title>
    <link rel="stylesheet" href="../assets/css/templatesmsw.css" />
</head>
<body>

    <h3>Daftar Template Surat</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>Nama Template</th>
            <th>Syarat</th>
            <th>Download</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['nama_template']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['syarat'])) ?></td>
            <td>
                <a class="download-link" href="../download.php?file=<?= urlencode($row['file_path']) ?>" download>Unduh</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="button-group">
        <a href="dashboard.php"><button>Kembali</button></a>
        <a href="buat_surat.php"><button>Buat Surat</button></a>
    </div>

    <script src="../assets/js/templatesmsw.js"></script>
</body>
</html>
