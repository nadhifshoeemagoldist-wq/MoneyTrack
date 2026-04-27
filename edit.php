<?php
session_start();
require_once 'koneksi.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$error = '';

if (!isset($_GET['id'])) {
    header("Location: transaksi.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    header("Location: transaksi.php");
    exit;
}

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
    } else {
        $stmt = $pdo->prepare("UPDATE transaksi SET kategori_id = ?, tipe = ?, jumlah = ?, tanggal = ?, deskripsi = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$kategori_id, $tipe, $jumlah, $tanggal, $deskripsi, $id, $user_id])) {
            header("Location: transaksi.php");
            exit;
        } else {
            $error = "Gagal memperbarui transaksi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - MoneyTrack</title>
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
                            <option value="income" <?= $transaksi['tipe'] == 'income' ? 'selected' : '' ?>>Pemasukan</option>
                            <option value="expense" <?= $transaksi['tipe'] == 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-control" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $transaksi['kategori_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required min="1" step="any" value="<?= htmlspecialchars($transaksi['jumlah']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= htmlspecialchars($transaksi['tanggal']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($transaksi['deskripsi']) ?></textarea>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Perbarui Transaksi</button>
                        <a href="transaksi.php" class="btn btn-outline" style="flex: 1; justify-content: center;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
