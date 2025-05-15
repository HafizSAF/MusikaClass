<?php
session_start();
include 'includes/config.php';

// Ambil data instruktur untuk ditampilkan di homepage
$instruktur = $pdo->query("SELECT * FROM instruktur")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MusikaClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Musika<span>Class</a>
            </div>
            <ul class="menu">
                <li class="tentang-h"><a class="tentang" href="index.php">Beranda</a></li>
                <li class="biasa-h"><a class="biasa" href="tentang.php">Tentang Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="atas">
            <div class="atas-content">
                <h1>Musika<span>Class</h1>
                <h2>Wujudkan Impian Musikmu Bersama Kami!</h2>
                <p>Selamat datang di MusikaClass, tempat belajar musik terbaik yang akan membawa bakat Anda ke level berikutnya. Kami hadir dengan instruktur berpengalaman dan fasilitas modern untuk membantu Anda menguasai instrumen musik pilihan.</p>
                <a href="daftar_kelas.php"><button>Daftar Sekarang ⭢</button></a>
            </div>
            <div class="atas-image">
                <img src="foto/imgatas.png" alt="animasi">
            </div>
        </section>
        
        <section class="tengah">
            <div class="tengah-header">
                <h2>Mengapa Memilih Kami?</h2>
            </div>
            <div class="tengah-grid">
                        <!-- Kotak Pertama -->
                        <div class="feature-box1">
                            <img src="foto/instruktur.png" alt="Instruktur Profesional">
                            <h2>Instruktur Profesional & Kurikulum Fleksibel</h2>
                            <p>Kami memiliki tim instruktur musik yang ahli dan berdedikasi, siap membantu Anda berkembang dengan pendekatan personal dan menyenangkan. Kurikulum kami dirancang fleksibel dan terstruktur, cocok untuk semua tingkat kemampuan mulai dari pemula hingga musisi berpengalaman.</p>
                        </div>
                <div class="kolom">
                    <div class="baris">
                        <!-- Kotak Kedua -->
                        <div class="feature-box2">
                            <img src="foto/kelas-tatap-muka.png" alt="Kelas Tatap Muka">
                            <h2>Kelas Tatap Muka</h2>
                            <p>Belajar langsung di studio kami memberikan pengalaman interaktif yang lebih baik dan memungkinkan Anda mendapatkan umpan balik langsung dari instruktur.</p>
                        </div>
                        <!-- Kotak Ketiga -->
                        <div class="feature-box2">
                            <img src="foto/lingkungan-belajar.png" alt="Lingkungan Belajar yang Inspiratif">
                            <h2>Lingkungan Belajar yang Inspiratif</h2>
                            <p>Kami menciptakan suasana yang mendukung dan menyenangkan untuk membantu Anda belajar dengan percaya diri dan maksimal.</p>
                        </div>
                    </div>
                        <!-- Kotak Keempat -->
                        <div class="feature-box3">
                            <div class="isibox3">
                            <h2>Fokus pada Empat Instrumen</h2>
                            <p>Kami menawarkan kursus khusus untuk vokal, drum, keyboard, dan gitar elektrik, memastikan Anda mendapatkan pembelajaran yang mendalam.</p>
                            </div>
                            <img src="foto/fokus-instrumen.png" alt="Fokus pada Empat Instrumen">
                        </div>
                </div>
            </div>
        </section>

        <!--Section Testi-->
        <section class="testimonials">
            <div class="testi-head">
                <h2>Testimoni</h2>
            </div>
            <div class="testimoni">
                <!-- Testimoni Pertama -->
                <div class="testimonial-card">
                    <div class="testi-row">
                        <img src="foto/Avatar1.png" alt="Andi" class="avatar">
                        <p class="name">Andi, 25 tahun</p>
                    </div>
                    <div class="info">
                        <p class="quote">“Saya tidak pernah menyangka bisa memainkan keyboard dengan lancar dalam waktu singkat. Instruktur di sini sangat sabar dan metode pengajarannya sangat efektif!”</p>
                    </div>
                </div>

                <!-- Testimoni Kedua -->
                <div class="testimonial-card2">
                    <div class="testi-row">
                        <img src="foto/Avatar2.png" alt="Rina" class="avatar">
                        <p class="name">Rina, 19 tahun</p>
                    </div>
                    <div class="info">
                        <p class="quote">“Sejak bergabung di sini, saya merasa lebih percaya diri di depan umum. Terima kasih atas bimbingannya!”</p>
                    </div>
                </div>

                <!-- Testimoni Ketiga -->
                <div class="testimonial-card">
                    <div class="testi-row">
                        <img src="foto/Avatar3.png" alt="Ibu Siska" class="avatar">
                        <p class="name">Ibu Siska, <br>Orang Tua Siswa</p>
                    </div>
                    <div class="info">
                        <p class="quote">“Anak saya semakin antusias belajar drum setelah mengikuti kursus di sini. Instrutornya ramah dan sangat menginspirasi.”</p>
                    </div>
                </div>
            </div>
        </section>

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
</body>
</html>