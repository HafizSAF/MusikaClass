<?php
include 'includes/config.php';

$order_id = $_GET['order_id'];
$status = $_GET['status'];

// Update status pembayaran di database
$stmt = $pdo->prepare("UPDATE pembayaran SET status = ? WHERE id = ?");
$stmt->execute([$status, str_replace('ORDER-', '', $order_id)]);

if ($status == 'success') {
    echo "<script>alert('Pembayaran berhasil!'); window.location='profile.php';</script>";
} elseif ($status == 'pending') {
    echo "<script>alert('Pembayaran sedang diproses.'); window.location='profile.php';</script>";
} else {
    echo "<script>alert('Pembayaran gagal. Silakan coba lagi.'); window.location='pembayaran.php';</script>";
}
?>