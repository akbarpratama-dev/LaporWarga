<?php
session_start();
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - LaporWarga</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/style.css?v=2.1">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>LaporWarga</h1>
                <p>Admin Panel</p>
            </div>

            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    if($_GET['error'] == 'invalid') echo "Username atau password salah!";
                    else if($_GET['error'] == 'unauthorized') echo "Silakan login terlebih dahulu!";
                ?>
            </div>
            <?php endif; ?>

            <form action="../process/admin_login_process.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="login-footer">
                <a href="../public/index.php"><i class="ri-arrow-left-line"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
