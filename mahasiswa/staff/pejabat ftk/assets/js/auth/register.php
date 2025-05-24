<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Pengguna</title>
</head>
<body>
    <h2>Form Registrasi</h2>
    <form action="proses_register.php" method="post">
        <label>Nama:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Role:</label><br>
        <select name="role" required>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="staff_tu">Staff TU</option>
            <option value="pejabat_fakultas">Pejabat Fakultas</option>
        </select><br><br>

        <input type="submit" value="Daftar">
    </form>
    <a href="login.php"><button>Sudah Ada Akun</button></a>
</body>
</html>
