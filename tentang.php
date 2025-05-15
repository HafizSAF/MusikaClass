<?php
session_start();
include 'includes/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MusikaClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tentang.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Musika<span>Class</a>
            </div>
            <ul class="menu">
                <li class="biasa-h"><a class="biasa" href="index.php" id="home-link">Beranda</a></li>
                <li class="tentang-h"><a class="tentang" href="tentang.php">Tentang Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="atas">
            <div class="atas-content">
                <h1>Profil Lembaga Kursus</h1>
                <h2>Musika<span>Class</h2>
                <p>MusikaClass, tempat di mana musik menjadi bahasa universal untuk menginspirasi, mengembangkan bakat, dan membawa kebahagiaan bagi setiap individu. Didirikan pada tahun 2025, kami telah berkomitmen untuk memberikan pendidikan musik berkualitas tinggi kepada siswa dari segala usia dan latar belakang.</p>
                <p>Kami percaya bahwa setiap orang memiliki potensi besar dalam dunia musik. Oleh karena itu, kami menyediakan lingkungan belajar yang mendukung, instruktur yang berpengalaman, dan fasilitas modern untuk membantu Anda mencapai tujuan bermusik Anda.</p>
                <p>Dari kelas privat, pemula hingga profesional, MusikaClass adalah tempat di mana impian bermusik Anda dimulai.</p>
            </div>
        </section>

        <section class="vismi">
            <div class="vismi-content" >
                <div class="visi">
                    <h3>Visi Kami</h3>
                    <p>Menjadi lembaga kursus musik terdepan yang menginspirasi generasi muda dan dewasa untuk menggali potensi musik mereka secara maksimal, serta menciptakan komunitas pecinta musik yang kreatif, inklusif, dan berdedikasi.</p>
                </div>
                <div class="misi">
                    <h3>Misi Kami</h3>
                    <p>1.Mengembangkan bakat musisi masa depan
                        <br>2. Memberikan Pengalaman Belajar yang Personal
                        <br>3. Menciptakan Lingkungan Belajar yang Inklusif
                        <br>4. Mendorong Kreativitas dan Ekspresi Diri
                        <br>5. Berbagi Nilai Positif Melalui Musik
                    </p>
                </div>
            </div>
        </section>

        <section class="ins">
            <div class="ins-head">
                <h2>Profil Instruktur</h2>
            </div>
            <div class="ins-content">
                <div class="card-r">
                    <div class="card-ins">
                        <div class="card-judul">
                            <p>Vokal</p>
                        </div>
                        <div class="card-info">
                            <p>Nadia Syifa <br><span>Penyanyi profesional yang telah berkarier di industri musik selama 10 tahun. Ahli dalam teknik pernapasan, kontrol nada, dan ekspresi emosional saat bernyanyi. </span></p>
                        </div>
                        <div class="card-rio">
                            <p>Rio Mahendra <br><span>Vocal coach profesional yang telah berkarier di industri musik selama 11 tahun. Ahli dalam teknik power vocal, improvisasi nada, dan membantu siswa menemukan karakter suara mereka sendiri.</span></p>
                        </div>
                    </div>
                    <div class="card-ins">
                        <div class="card-judul">
                            <p>Drum</p>
                        </div>
                        <div class="card-info2">
                            <p>Bayu Aditya <br><span>Drummer handal yang telah bermain di berbagai band terkenal. Memiliki pengalaman mengajar drum selama 8 tahun dan dikenal karena kemampuannya dalam mengajarkan ritme dan pola drum secara sistematis.</span></p>
                        </div>
                    </div>
                </div>
                <div class="card-r">
                    <div class="card-ins">
                        <div class="card-judul">
                            <p>Keyboard</p>
                        </div>
                        <div class="card-info2">
                            <p>Sinta Lestari <br><span>Seorang pianis klasik dan keyboardist modern yang telah mengajar selama lebih dari 12 tahun. Memiliki kemampuan luar biasa dalam membimbing siswa dari pemula hingga tingkat mahir.</span></p>
                        </div>
                    </div>
                    <div class="card-ins">
                        <div class="card-judul">
                            <p>Gitar Elektrik</p>
                        </div>
                        <div class="card-info2">
                            <p>Andi Pratama <br><span>Gitaris profesional dengan pengalaman lebih dari 9 tahun di dunia musik. Telah tampil di berbagai acara musik besar dan memiliki keahlian dalam berbagai genre, mulai dari rock hingga jazz. Metode pengajarannya yang interaktif membuat belajar gitar elektrik menjadi menyenangkan dan mudah dipahami.</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="program">
            <div class="card-head">
                <h2>Program Kursus</h2>
            </div>
            <div class="program-content">
                <div class="prog-card">
                    <div class="prog-judul">
                        <p>Vokal</p>
                    </div>
                    <div class="prog-info">
                        <p>Program ini mencakup teknik pernapasan, pitch control, dan ekspansi range vokal untuk memaksimalkan potensi suara Anda.</p>
                    </div>
                </div>
                <div class="prog-card">
                    <div class="prog-judul">
                        <p>Drum</p>
                    </div>
                    <div class="prog-info">
                        <p>Pelajari ritme, koordinasi, dan teknik dasar hingga pola ritme kompleks untuk berbagai genre musik.</p>
                    </div>
                </div>
                <div class="prog-card">
                    <div class="prog-judul">
                        <p>Gitar Elektrik</p>
                    </div>
                    <div class="prog-info">
                        <p>Pelajari teknik picking, fingering, chord progression, dan improvisasi untuk berbagai genre musik.</p>
                    </div>
                </div>
                <div class="prog-card">
                    <div class="prog-judul">
                        <p>Keyboard</p>
                    </div>
                    <div class="prog-info">
                        <p>Kuasai permainan keyboard mencakup pembacaan notasi, pengembangan teknik bermain, dan eksplorasi sound.</p>
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