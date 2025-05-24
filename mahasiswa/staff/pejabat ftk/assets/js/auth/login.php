<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Surat Fakultas</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form id="loginForm" action="proses_login.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <input type="submit" value="Login">
        </form>
        <a href="register.php"><button type="button">Akun Baru</button></a>
    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>
