<?php
include 'includes/config.php';

// Hapus semua entri kelas yang ditolak lebih dari 30 detik yang lalu
$stmt = $pdo->prepare("DELETE FROM kelas WHERE status = 'ditolak' AND deleted_at < NOW() - INTERVAL 30 SECOND");
$stmt->execute();

echo "Semua kelas yang ditolak lebih dari 30 detik telah dihapus.";
?>