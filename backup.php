<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query untuk menampilkan kelas yang masih aktif atau yang ditolak dalam 30 detik terakhir
$query = "
    SELECT k.*, i.nama AS instruktur_nama, i.rating AS instruktur_rating 
    FROM kelas k 
    JOIN instruktur i ON k.instruktur_id = i.id 
    WHERE k.user_id = :user_id 
    AND (k.deleted_at IS NULL OR (k.status = 'ditolak' AND k.deleted_at > NOW() - INTERVAL 30 SECOND))
";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$kelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $instruktur_id = $_POST['instruktur_id'];
    $rating = $_POST['rating'];

    // Periksa apakah user sudah memberikan rating untuk instruktur ini
    $stmt = $pdo->prepare("SELECT * FROM rating_instruktur WHERE user_id = ? AND instruktur_id = ?");
    $stmt->execute([$user_id, $instruktur_id]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Anda sudah memberikan rating untuk instruktur ini!');</script>";
    } else {
        // Simpan rating
        $stmt = $pdo->prepare("INSERT INTO rating_instruktur (user_id, instruktur_id, rating) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $instruktur_id, $rating]);

        // Hitung rata-rata rating
        $stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating FROM rating_instruktur WHERE instruktur_id = ?");
        $stmt->execute([$instruktur_id]);
        $avg_rating = $stmt->fetchColumn();

        // Update rating instruktur
        $stmt = $pdo->prepare("UPDATE instruktur SET rating = ? WHERE id = ?");
        $stmt->execute([$avg_rating, $instruktur_id]);

        echo "<script>alert('Rating berhasil disimpan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Musika<span>Class</a>
            </div>
            <ul class="menu">
                <li class="biasa-h"><a class="biasa" href="index.php">Beranda</a></li>
                <li class="biasa-h"><a class="biasa" href="tentang.php">Tentang Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="tentang-h"><a class="tentang" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="welcoming">
            <h1>Selamat Datang di Les Musik Online</h1>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p>Halo, 
                        <?php
                        // Tampilkan nama jika tersedia
                        if (!empty($_SESSION['nama'])) {
                            echo htmlspecialchars($_SESSION['nama']);
                        } else {
                            echo "Pengguna"; // Default jika nama kosong
                        }
                        ?>!
                    </p>
                <?php else: ?>
                <p><a href="login.php">Login</a> | <a href="register.php">Daftar</a></p>
                <?php endif; ?>
            <h1>Profile</h1>
            <p>
                <a href="index.php">Kembali ke Beranda</a> | 
                <a href="logout.php">Logout</a>
            </p>
        </section>

        <h2>Kelas yang Diikuti:</h2>
        <ul>
            <?php if (empty($kelas)): ?>
                <li>Tidak ada kelas yang diikuti.</li>
            <?php else: ?>
                <?php foreach ($kelas as $k): ?>
                    <li>
                        <?= htmlspecialchars($k['instruktur_nama']) ?> - <?= $k['hari'] ?> <?= date('H:i', strtotime($k['jam'])) ?> 
                        (Status: <?= htmlspecialchars($k['status']) ?>)
                    </li>
                    <?php if ($k['status'] === 'disetujui'): ?>
                        <?php
                        // Periksa apakah user sudah memberikan rating untuk instruktur ini
                        $stmt = $pdo->prepare("SELECT * FROM rating_instruktur WHERE user_id = ? AND instruktur_id = ?");
                        $stmt->execute([$user_id, $k['instruktur_id']]);
                        $has_rated = $stmt->rowCount() > 0;
                        ?>

                        <?php if (!$has_rated): ?>
                            <form method="POST">
                                <input type="hidden" name="instruktur_id" value="<?= $k['instruktur_id'] ?>">
                                Rating: 
                                <select name="rating" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <button type="submit">Kirim Rating</button>
                            </form>
                        <?php else: ?>
                            <p>Anda sudah memberikan rating untuk instruktur ini.</p>
                        <?php endif; ?>
                    <?php elseif ($k['status'] === 'ditolak'): ?>
                        <p>Status: Ditolak.</p>
                        <?php if (!empty($k['pesan_penolakan'])): ?>
                            <p>Pesan Admin: <?= htmlspecialchars($k['pesan_penolakan']) ?></p>
                        <?php endif; ?>
                        <p>Pendaftaran ini akan dihapus dalam 30 detik.</p>
                    <?php else: ?>
                        <p>Status: Menunggu Persetujuan</p>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <!---Section Footer-->
        <section class="footer">
            <div class="footer-content">
                <div class="judul">
                    <h2>Musika<span>Class</span></h2>
                </div>
                <div class="link-kosong">
                    <p>FAQ</p>
                </div>
                <div class="lokasi">
                    <p> Jl. Melodi No. 123, Jakarta Selatan. (012) 333-4567.</p>
                </div>
                <div class="copyright">
                    <p>&copy;  MusikaClass 2025. All Rights Reserved.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>



<!---Backup Daftar --->
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

<!---Part 2--->
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

// Debugging: Cek input formulir
echo "Instruktur ID: " . htmlspecialchars($instruktur_id) . "<br>";
echo "Hari: " . htmlspecialchars($hari) . "<br>";
echo "Jam: " . htmlspecialchars($jam) . "<br>";

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
    echo "<script>
        alert('Jadwal sudah terisi! Silakan pilih hari dan jam lain.');
        window.location.href = 'pilih_instruktur.php';
    </script>";
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
    echo "<script>
        alert('Maaf, Anda sudah memiliki jadwal pada hari dan jam tersebut.');
        window.location.href = 'pilih_instruktur.php';
    </script>";
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

// Redirect ke halaman pembayaran dengan kelas_id
header("Location: pembayaran.php?kelas_id=$kelas_id");
exit;
?>