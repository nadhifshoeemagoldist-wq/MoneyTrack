<?php
session_start();
require_once 'koneksi.php';
checkLogin();

$user_id = $_SESSION['user_id'];

// Logic for Reports View
$stmt = $pdo->prepare("SELECT 
    COALESCE(SUM(CASE WHEN tipe = 'income' THEN jumlah ELSE 0 END), 0) as total_income,
    COALESCE(SUM(CASE WHEN tipe = 'expense' THEN jumlah ELSE 0 END), 0) as total_expense
    FROM transaksi WHERE user_id = ?");
$stmt->execute([$user_id]);
$totals = $stmt->fetch();

$total_income = $totals['total_income'];
$total_expense = $totals['total_expense'];
$total_savings = $total_income - $total_expense;

// Get Trend Data (Last 6 months)
$months = [];
$income_trend = [];
$expense_trend = [];

for ($i = 5; $i >= 0; $i--) {
    $m = date('Y-m', strtotime("-$i months"));
    $months[] = date('M', strtotime("-$i months"));
    
    $stmt = $pdo->prepare("SELECT 
        COALESCE(SUM(CASE WHEN tipe = 'income' THEN jumlah ELSE 0 END), 0) as inc,
        COALESCE(SUM(CASE WHEN tipe = 'expense' THEN jumlah ELSE 0 END), 0) as exp
        FROM transaksi WHERE user_id = ? AND DATE_FORMAT(tanggal, '%Y-%m') = ?");
    $stmt->execute([$user_id, $m]);
    $row = $stmt->fetch();
    $income_trend[] = $row['inc'];
    $expense_trend[] = $row['exp'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - MoneyTrack</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'topbar.php'; ?>

        <div class="content-body">
            <!-- REPORTS VIEW -->
            <div class="summary-grid">
                <div class="card card-summary">
                    <div class="summary-info">
                        <h4>Total Pemasukan</h4>
                        <div class="value"><?= formatRupiah($total_income) ?></div>
                    </div>
                    <div class="summary-icon icon-income"><i class="fas fa-chart-line"></i></div>
                </div>
                <div class="card card-summary">
                    <div class="summary-info">
                        <h4>Total Pengeluaran</h4>
                        <div class="value"><?= formatRupiah($total_expense) ?></div>
                    </div>
                    <div class="summary-icon icon-expense"><i class="fas fa-chart-area"></i></div>
                </div>
                <div class="card card-summary card-green">
                    <div class="summary-info">
                        <h4>Total Tabungan</h4>
                        <div class="value"><?= formatRupiah($total_savings) ?></div>
                    </div>
                    <div class="summary-icon"><i class="fas fa-piggy-bank"></i></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="border:none; padding: 0 0 20px 0;">
                    <h3 style="font-size: 1.1rem;">Tren Pemasukan & Pengeluaran</h3>
                </div>
                <div style="position: relative; height:400px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <script>
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($months) ?>,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: <?= json_encode($income_trend) ?>,
                                borderColor: '#22C55E',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 6,
                                pointBackgroundColor: '#22C55E'
                            },
                            {
                                label: 'Pengeluaran',
                                data: <?= json_encode($expense_trend) ?>,
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 6,
                                pointBackgroundColor: '#EF4444'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            </script>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
