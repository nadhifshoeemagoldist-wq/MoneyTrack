<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <a href="index.php" class="sidebar-logo">
        <i class="fas fa-wallet"></i>
        <span>MoneyTrack</span>
    </a>
    
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        <a href="transaksi.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'transaksi.php' || basename($_SERVER['PHP_SELF']) == 'tambah.php' || basename($_SERVER['PHP_SELF']) == 'edit.php' ? 'active' : '' ?>">
            <i class="fas fa-exchange-alt"></i>
            <span>Transaksi</span>
        </a>
        <a href="laporan.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Laporan</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>
