<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Password tidak cocok!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hash])) {
                $success = "Akun berhasil dibuat! Silakan login.";
            } else {
                $error = "Pendaftaran gagal.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MoneyTrack</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: white;">
    <div class="auth-container">
        <div class="auth-sidebar">
            <div style="font-size: 3rem; margin-bottom: 20px;"><i class="fas fa-wallet"></i></div>
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">MoneyTrack</h1>
            <p style="font-size: 1.1rem; opacity: 0.9; text-align: center;">Mulai perjalanan finansialmu bersama kami hari ini.</p>
        </div>
        <div class="auth-content">
            <h2 style="font-size: 2rem; margin-bottom: 10px;">Buat Akun</h2>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Daftar untuk mulai mencatat pengeluaranmu.</p>

            <?php if ($error): ?>
                <div style="background: var(--danger-light); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background: var(--primary-light); color: var(--primary); padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Pilih username">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Buat password">
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px; margin-top: 10px;">Daftar Sekarang</button>
            </form>

            <p style="text-align: center; margin-top: 30px; font-size: 0.9rem; color: var(--text-muted);">
                Sudah punya akun? <a href="login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Masuk di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
