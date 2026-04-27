<?php
session_start();
require_once 'koneksi.php';
checkLogin();

$user_id = $_SESSION['user_id'];

// Get totals
$stmt = $pdo->prepare("SELECT 
    COALESCE(SUM(CASE WHEN tipe = 'income' THEN jumlah ELSE 0 END), 0) as total_income,
    COALESCE(SUM(CASE WHEN tipe = 'expense' THEN jumlah ELSE 0 END), 0) as total_expense
    FROM transaksi WHERE user_id = ?");
$stmt->execute([$user_id]);
$totals = $stmt->fetch();

$total_income = $totals['total_income'];
$total_expense = $totals['total_expense'];
$balance = $total_income - $total_expense;

// Get recent transactions
$stmt = $pdo->prepare("SELECT t.*, k.nama_kategori 
    FROM transaksi t 
    JOIN kategori k ON t.kategori_id = k.id 
    WHERE t.user_id = ? 
    ORDER BY t.tanggal DESC, t.id DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_transactions = $stmt->fetchAll();

// Get data for charts (Expense by category)
$stmt = $pdo->prepare("SELECT k.nama_kategori, SUM(t.jumlah) as total 
    FROM transaksi t 
    JOIN kategori k ON t.kategori_id = k.id 
    WHERE t.user_id = ? AND t.tipe = 'expense' 
    GROUP BY k.id");
$stmt->execute([$user_id]);
$expense_categories = $stmt->fetchAll();

$chart_cat_labels = [];
$chart_cat_data = [];
foreach ($expense_categories as $ec) {
    $chart_cat_labels[] = $ec['nama_kategori'];
    $chart_cat_data[] = $ec['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MoneyTrack</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'topbar.php'; ?>

        <div class="content-body">
            <div class="summary-grid">
                <div class="card card-summary card-green">
                    <div class="summary-info">
                        <h4>Total Saldo</h4>
                        <div class="value"><?= formatRupiah($balance) ?></div>
                    </div>
                    <div class="summary-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="card card-summary">
                    <div class="summary-info">
                        <h4>Total Pemasukan</h4>
                        <div class="value"><?= formatRupiah($total_income) ?></div>
                    </div>
                    <div class="summary-icon icon-income">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="card card-summary">
                    <div class="summary-info">
                        <h4>Total Pengeluaran</h4>
                        <div class="value"><?= formatRupiah($total_expense) ?></div>
                    </div>
                    <div class="summary-icon icon-expense">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="card">
                    <div class="card-header" style="border:none; padding: 0 0 20px 0;">
                        <h3 style="font-size: 1.1rem;">Pemasukan vs Pengeluaran</h3>
                    </div>
                    <div style="position: relative; height:250px;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" style="border:none; padding: 0 0 20px 0;">
                        <h3 style="font-size: 1.1rem;">Pengeluaran per Kategori</h3>
                    </div>
                    <div style="position: relative; height:250px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card table-card" style="margin-top: 24px;">
                <div class="table-header">
                    <h3 style="font-size: 1.1rem;">Transaksi Terakhir</h3>
                    <a href="transaksi.php" class="btn btn-outline btn-sm">Lihat Semua</a>
                </div>
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
                            <?php if (count($recent_transactions) > 0): ?>
                                <?php foreach ($recent_transactions as $t): ?>
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
                                    <td colspan="5" style="text-align: center; padding: 40px;">Belum ada transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // Pie Chart
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Pemasukan', 'Pengeluaran'],
                datasets: [{
                    data: [<?= $total_income ?>, <?= $total_expense ?>],
                    backgroundColor: ['#22C55E', '#EF4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });

        // Bar Chart
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_cat_labels) ?>,
                datasets: [{
                    label: 'Pengeluaran',
                    data: <?= json_encode($chart_cat_data) ?>,
                    backgroundColor: '#22C55E',
                    borderRadius: 8,
                    barThickness: 25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { display: true, drawBorder: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>
