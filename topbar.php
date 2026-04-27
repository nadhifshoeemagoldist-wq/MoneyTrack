<?php
$title = "Dashboard";
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page == 'transaksi.php') $title = "Daftar Transaksi";
if ($current_page == 'laporan.php') $title = "Laporan Keuangan";
if ($current_page == 'tambah.php') $title = "Tambah Transaksi";
if ($current_page == 'edit.php') $title = "Edit Transaksi";

$initials = strtoupper(substr($_SESSION['username'], 0, 1));
if (strlen($_SESSION['username']) > 1) {
    $initials = strtoupper(substr($_SESSION['username'], 0, 1) . substr($_SESSION['username'], 1, 1));
}
?>
<header class="topbar">
    <div class="page-title"><?= $title ?></div>
    
    <div class="user-profile">
        <div class="user-info" style="text-align: right;">
            <div class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
            <div class="user-email"><?= htmlspecialchars($_SESSION['username']) ?>@example.com</div>
        </div>
        <div class="avatar"><?= $initials ?></div>
    </div>
</header>
