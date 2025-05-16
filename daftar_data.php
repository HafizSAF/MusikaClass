<?php
session_start();
include 'includes/config.php';

// Cek apakah user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Simpan kelas jika ada POST kelas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kelas'])) {
    $_SESSION['kelas'] = $_POST['kelas'];
}

// Jika belum pilih kelas, arahkan ke daftar_kelas.php
if (!isset($_SESSION['kelas'])) {
    header("Location: daftar_kelas.php");
    exit;
}

// Ambil data user dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User tidak ditemukan di database.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $pendidikan   = trim($_POST['pendidikan'] ?? '');
    $nomor_hp     = trim($_POST['nomor_hp'] ?? '');

    if (!empty($nama_lengkap) && !empty($pendidikan) && !empty($nomor_hp)) {
        // Update hanya jika semua field terisi
        $updateStmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, pendidikan = ?, nomor_hp = ? WHERE id = ?");
        $updateStmt->execute([$nama_lengkap, $pendidikan, $nomor_hp, $user_id]);

        // Ambil ulang data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$data_diri_terisi = 
    !empty($user['nama_lengkap']) && 
    !empty($user['pendidikan']) && 
    !empty($user['nomor_hp']);

if ($data_diri_terisi) {
    header("Location: pilih_instruktur.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Isi Data Pendaftaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/data_daftar2.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Musika<span>Class</a>
            </div>

            <!-- Hamburger Button -->
            <button class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="menu" id="menu">
                <li class="biasa-h"><a class="biasa" href="index.php">Beranda</a></li>
                <li class="biasa-h"><a class="biasa" href="tentang.php">Tentang Kursus</a></li>
                <li class="tentang-h"><a class="tentang" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Judul Page --->
        <div class="judul-page">
            <h1>Isi Data Pendaftaran</h1>
            <p><a class="tombol" href="daftar_kelas.php">Kembali ke Daftar Kelas</a></p>
        </div>

        <!-- Modal Box -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Anda harus mengisi data diri terlebih dahulu sebelum melanjutkan pendaftaran.</p>
            </div>
        </div>

        <form method="POST" class="form-data">
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>" required><br>

            <label for="pendidikan">Pendidikan Terakhir:</label>
            <input type="text" name="pendidikan" id="pendidikan" value="<?= htmlspecialchars($user['pendidikan'] ?? '') ?>" required><br>

            <label for="nomor_hp">Nomor HP:</label>
            <input type="text" name="nomor_hp" id="nomor_hp" value="<?= htmlspecialchars($user['nomor_hp'] ?? '') ?>" required><br>

            <button type="submit">Simpan Data dan Lanjutkan</button>
        </form>

        <!---Section Footer-->
        <section class="footer">
            <div class="footer-content">
                <div class="judul">
                    <h2>Musika<span>Class</span></h2>
                </div>
                <div class="link-kosong">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Jika user sudah login -->
                        <a class="tmbl-faq" href="profile.php#faq">FAQ</a>
                    <?php else: ?>
                        <!-- Jika user belum login -->
                        <a class="tmbl-faq" href="login.php">FAQ</a>
                    <?php endif; ?>
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

    <script>
        // Fungsi untuk menampilkan modal
        function showModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "block";

            // Ketika tombol close (x) diklik
            var span = document.getElementsByClassName("close")[0];
            span.onclick = function() {
                modal.style.display = "none";
            }

            // Ketika area luar modal diklik
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }

        const hamburger = document.getElementById('hamburger');
        const menu = document.querySelector('.menu');

        hamburger.addEventListener('click', () => {
            menu.classList.toggle('active');
        });

        // Cek apakah pengguna sudah mengisi data diri
        <?php if (!$data_diri_terisi): ?>
            window.onload = function() {
                showModal();
            }
        <?php endif; ?>
    </script>
</body>
</html>