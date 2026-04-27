<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MoneyTrack</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: white;">
    <div class="auth-container">
        <div class="auth-sidebar">
            <div style="font-size: 3rem; margin-bottom: 20px;"><i class="fas fa-wallet"></i></div>
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">MoneyTrack</h1>
            <p style="font-size: 1.1rem; opacity: 0.9; text-align: center;">Kelola keuanganmu dengan cara yang simpel dan powerful.</p>
        </div>
        <div class="auth-content">
            <h2 style="font-size: 2rem; margin-bottom: 10px;">Selamat Datang!</h2>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Silakan masuk untuk melanjutkan.</p>

            <?php if ($error): ?>
                <div style="background: var(--danger-light); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px; margin-top: 10px;">Masuk Sekarang</button>
            </form>

            <p style="text-align: center; margin-top: 30px; font-size: 0.9rem; color: var(--text-muted);">
                Belum punya akun? <a href="register.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Daftar Gratis</a>
            </p>
        </div>
    </div>
</body>
</html>
