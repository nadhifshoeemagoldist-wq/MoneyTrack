<?php
session_start();
require_once 'koneksi.php';
checkLogin();

$user_id = $_SESSION['user_id'];

// Logic for Transactions Table View
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$filter_kategori = isset($_GET['kategori_id']) ? $_GET['kategori_id'] : '';
$filter_tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT t.*, k.nama_kategori 
          FROM transaksi t 
          JOIN kategori k ON t.kategori_id = k.id 
          WHERE t.user_id = :user_id 
          AND t.tanggal BETWEEN :start_date AND :end_date";

$params = [':user_id' => $user_id, ':start_date' => $start_date, ':end_date' => $end_date];
if (!empty($filter_kategori)) { $query .= " AND t.kategori_id = :kategori_id"; $params[':kategori_id'] = $filter_kategori; }
if (!empty($filter_tipe)) { $query .= " AND t.tipe = :tipe"; $params[':tipe'] = $filter_tipe; }
if (!empty($search)) { $query .= " AND t.deskripsi LIKE :search"; $params[':search'] = "%$search%"; }

$query .= " ORDER BY t.tanggal DESC, t.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

$stmt_kat = $pdo->query("SELECT * FROM kategori");
$categories = $stmt_kat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - MoneyTrack</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'topbar.php'; ?>

        <div class="content-body">
            <div class="card" style="padding: 24px;">
                <form method="GET" action="transaksi.php" class="filter-section">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="form-label">Dari Tanggal</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-calendar-alt"></i>
                                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="form-label">Sampai Tanggal</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-calendar-check"></i>
                                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="form-label">Kategori</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-tag"></i>
                                <select name="kategori_id" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $filter_kategori == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nama_kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="form-label">Tipe</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-filter"></i>
                                <select name="tipe" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    <option value="income" <?= $filter_tipe == 'income' ? 'selected' : '' ?>>Pemasukan</option>
                                    <option value="expense" <?= $filter_tipe == 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <div class="filter-group" style="flex: 1;">
                            <label class="form-label">Cari Deskripsi</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" class="form-control" placeholder="Cari deskripsi transaksi..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 24px;">
                                <i class="fas fa-search" style="margin-right: 8px;"></i> Cari
                            </button>
                            <a href="transaksi.php" class="btn btn-outline" style="height: 48px; width: 48px; display: flex; align-items: center; justify-content: center;" title="Reset Filter">
                                <i class="fas fa-undo"></i>
                            </a>
                            <a href="tambah.php" class="btn btn-success" style="height: 48px; padding: 0 24px; display: flex; align-items: center; justify-content: center; background: #22C55E; color: white;" title="Tambah Transaksi">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($transactions) > 0): ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td style="color: var(--text-muted);"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                                        <td style="font-weight: 500;"><?= htmlspecialchars($t['deskripsi']) ?></td>
                                        <td>
                                            <span class="badge" style="background: #F3F4F6; color: var(--text-muted);">
                                                <?= htmlspecialchars($t['nama_kategori']) ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 600;" class="<?= $t['tipe'] == 'income' ? 'text-success' : 'text-danger' ?>">
                                            <?= $t['tipe'] == 'income' ? '+' : '-' ?><?= formatRupiah($t['jumlah']) ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-outline btn-icon" style="color: var(--primary);"><i class="fas fa-edit"></i></a>
                                                <a href="hapus.php?id=<?= $t['id'] ?>" class="btn btn-outline btn-icon btn-delete" style="color: var(--danger);"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">Tidak ada transaksi yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
