<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/config.php';

try {
    $query = "
        SELECT 
            i.id AS instruktur_id,
            i.nama AS nama_instruktur,
            i.kelas AS jenis_kelas,
            i.harga_kelas
        FROM 
            instruktur i;
    ";
    $stmt = $pdo->query($query);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Daftar Kelas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/daftar-kelas.css">
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
                <li class="biasa-h"><a class="biasa" href="index.php" id="home-link">Beranda</a></li>
                <li class="biasa-h"><a class="biasa" href="tentang.php">Tentang Kursus</a></li>
                <li class="tentang-h"><a class="tentang" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <Section class="card-content">
            <div class="content-judul">
                <h1>Daftar Kursus Musik di <span>Musika<span1>Class</h1>
            </div>
            <div class="card-container">
                <?php if (empty($kelas_list)): ?>
                    <p>Tidak ada kelas yang tersedia.</p>
                <?php else: ?>
                    <?php foreach ($kelas_list as $kelas): ?>
                        <div class="card">
                            <p class="judul_kelas"><?= htmlspecialchars($kelas['jenis_kelas']) ?><p>
                            <p class="info-kelas">Instruktur : <?= htmlspecialchars($kelas['nama_instruktur']) ?></p>
                            <p class="info-kelas">Jam : 11.00 - 17.00 (1 jam/orang)</p>
                            <p class="info-kelas">Hari : Senin - Sabtu</p>
                            <p class="info-kelas">Harga: Rp <?= number_format($kelas['harga_kelas'], 0, ',', '.') ?>/bulan</p>
                            <form method="POST" action="daftar_data.php">
                                <input type="hidden" name="kelas" value="<?= htmlspecialchars($kelas['jenis_kelas']) ?>">
                                <button type="submit">Daftar Sekarang →</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </Section>

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
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
    </script>

</body>
</html>