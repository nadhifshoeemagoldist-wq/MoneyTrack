<?php
session_start();
require_once 'koneksi.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Categories for dropdown
$stmt = $pdo->query("SELECT * FROM kategori");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah = str_replace(['Rp', '.', ' '], '', $_POST['jumlah']);
    $kategori_id = $_POST['kategori_id'];
    $tipe = $_POST['tipe'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    if (empty($jumlah) || empty($kategori_id) || empty($tipe) || empty($tanggal)) {
        $error = "Semua kolom wajib diisi!";
    } elseif (!is_numeric($jumlah) || $jumlah <= 0) {
        $error = "Jumlah tidak valid!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO transaksi (user_id, kategori_id, tipe, jumlah, tanggal, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $kategori_id, $tipe, $jumlah, $tanggal, $deskripsi])) {
            header("Location: transaksi.php");
            exit;
        } else {
            $error = "Gagal menambah transaksi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - MoneyTrack</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'topbar.php'; ?>

        <div class="content-body">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <?php if ($error): ?>
                    <div class="alert alert-error" style="background: var(--danger-light); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-control" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required min="1" step="any" placeholder="Rp..">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Contoh: Beli nasi uduk"></textarea>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Transaksi</button>
                        <a href="transaksi.php" class="btn btn-outline" style="flex: 1; justify-content: center;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
