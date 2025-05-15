<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Periksa apakah kelas sudah dipilih
if (!isset($_SESSION['kelas'])) {
    header("Location: daftar_kelas.php");
    exit;
}

include 'includes/config.php';

$user_id = $_SESSION['user_id'];
$instruktur_id = $_POST['instruktur_id'];
$hari = $_POST['hari'];
$jam = $_POST['jam'];

// Cek jadwal instruktur
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS count
    FROM kelas
    WHERE instruktur_id = ? AND hari = ? AND jam = ?
    AND status IN ('pending', 'disetujui') AND deleted_at IS NULL
");
$stmt->execute([$instruktur_id, $hari, $jam]);
$result_instruktur = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result_instruktur['count'] > 0) {
    // Simpan pesan error ke session
    $_SESSION['error_message'] = "Jadwal sudah terisi! Silakan pilih hari dan jam lain.";
    header("Location: pilih_instruktur.php");
    exit;
}

// Cek jadwal user
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS count
    FROM kelas
    WHERE user_id = ? AND hari = ? AND jam = ?
    AND status IN ('pending', 'disetujui') AND deleted_at IS NULL
");
$stmt->execute([$user_id, $hari, $jam]);
$result_user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result_user['count'] > 0) {
    // Simpan pesan error ke session
    $_SESSION['error_message'] = "Maaf, Anda sudah memiliki jadwal pada hari dan jam tersebut.";
    header("Location: pilih_instruktur.php");
    exit;
}

// Simpan data kelas
$stmt = $pdo->prepare("
    INSERT INTO kelas (user_id, instruktur_id, hari, jam, status)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$user_id, $instruktur_id, $hari, $jam, 'pending']);

// Ambil ID kelas yang baru saja dibuat
$kelas_id = $pdo->lastInsertId();

// Hapus pesan error jika ada
unset($_SESSION['error_message']);

// Redirect ke halaman pembayaran dengan kelas_id
header("Location: pembayaran.php?kelas_id=$kelas_id");
exit;
?>