<?php
session_start();
include 'includes/config.php';

if (!isset($_GET['kelas_id'])) {
    die("ID Kelas tidak ditemukan.");
}

$kelas_id = $_GET['kelas_id'];

// Ambil data kelas
$stmt = $pdo->prepare("SELECT * FROM kelas WHERE id = ?");
$stmt->execute([$kelas_id]);
$kelas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {
    die("Data kelas tidak ditemukan.");
}

// Ambil data user
$user_id = $kelas['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data instruktur
$instruktur_id = $kelas['instruktur_id'];
$stmt = $pdo->prepare("SELECT nama AS instruktur_nama FROM instruktur WHERE id = ?");
$stmt->execute([$instruktur_id]);
$instruktur = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins :wght@400;600&display=swap" rel="stylesheet">
    <meta content="telephone=no" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F4E1;
            color: #333;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #CBA135;
            margin-bottom: 20px;
        }
        ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 30px;
        }
        li {
            margin-bottom: 10px;
        }
        .btn-whatsapp {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-whatsapp:hover {
            background-color: #126c3e;
        }
        .payment-methods {
            margin-top: 20px;
        }
        .payment-methods h3 {
            color: #4E1F00;
            margin-bottom: 10px;
        }
        .payment-methods p {
            background: #f2f2f2;
            padding: 10px;
            border-left: 5px solid #CBA135;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Konfirmasi Pembayaran</h2>

    <p>Terima kasih telah mendaftar kelas berikut:</p>

    <ul>
        <li><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['nama_lengkap']) ?></li>
        <li><strong>Instruktur:</strong> <?= htmlspecialchars($instruktur['instruktur_nama'] ?? 'Tidak Diketahui') ?></li>
        <li><strong>Hari & Jam:</strong> <?= htmlspecialchars($kelas['hari']) ?>, <?= date('H:i', strtotime($kelas['jam'])) ?></li>
    </ul>

    <p>Silakan lakukan pembayaran melalui salah satu metode berikut:</p>

    <!-- Metode Pembayaran -->
    <div class="payment-methods">
        <h3>Metode Pembayaran</h3>

        <!-- ATM -->
        <h4>Transfer Bank (ATM)</h4>
        <p><strong>BCA</strong>: 7771234567 a.n. MusikaClass</p>
        <p><strong>BRI</strong>: 1234567890 a.n. MusikaClass</p>
        <p><strong>Mandiri</strong>: 10987654321 a.n. MusikaClass</p>

        <!-- DANA -->
        <h4>DANA</h4>
        <p>+62 812-3456-7890</p>

        <!-- GOPAY -->
        <h4>GOPAY</h4>
        <p>+62 812-1111-2222</p>
    </div>

    <p>Selanjutnya, konfirmasi pembayaran Anda ke admin via WhatsApp.</p>

    <!-- Tombol Konfirmasi ke WhatsApp -->
    <?php
    // Format pesan WhatsApp
    $user_nama = $user['nama_lengkap'];
    $instruktur_nama = $instruktur['instruktur_nama'] ?? 'Tidak Diketahui';
    $jadwal = $kelas['hari'] . ', ' . date('H:i', strtotime($kelas['jam']));
    ?>

    <?php
    // Buat URL WhatsApp
    $pesan_wa = "Halo Admin,\n\nSaya sudah melakukan pembayaran untuk kelas berikut:\nNama: {$user_nama}\nInstruktur: {$instruktur_nama}\nJadwal: {$jadwal}\n\nMohon diproses.";
    $nomor_wa = "+6285947525593"; // Ganti dengan nomor admin kamu
    $url_wa = "https://api.whatsapp.com/send?phone= " . urlencode($nomor_wa) . "&text=" . urlencode($pesan_wa);
    ?>

    <!-- Tombol Konfirmasi ke WhatsApp -->
    <a href="<?= $url_wa ?>" target="_blank" class="btn-whatsapp">💬 Konfirmasi ke WhatsApp</a>

    <br><br>
    <a href="profile.php" style="color: #4E1F00; text-decoration: underline;">← Kembali ke Profile</a>
</div>

</body>
</html>