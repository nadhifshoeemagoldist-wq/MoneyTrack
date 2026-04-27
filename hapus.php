<?php
session_start();
require_once 'koneksi.php';
checkLogin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Delete only if it belongs to the logged in user
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
}

// Redirect back to referring page or transaksi.php
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: transaksi.php");
}
exit;
?>
