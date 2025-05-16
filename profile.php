<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Query untuk kelas aktif atau ditolak < 30 detik dari database
$query = "
    SELECT k.*, i.nama AS instruktur_nama 
    FROM kelas k 
    JOIN instruktur i ON k.instruktur_id = i.id 
    WHERE k.user_id = :user_id 
    AND (
        k.deleted_at IS NULL OR 
        (k.status = 'ditolak' AND k.deleted_at > NOW() - INTERVAL 30 SECOND)
    )
";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$kelas_aktif = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gabungkan dengan kelas dari session
$kelas_session = [];

if (isset($_SESSION['kelas_ditolak']) && is_array($_SESSION['kelas_ditolak'])) {
    foreach ($_SESSION['kelas_ditolak'] as $kelas_id => $data_kelas) {
        if (is_array($data_kelas) && isset($data_kelas['kelas'], $data_kelas['timestamp'])) {
            $kelas_info = $data_kelas['kelas'];
            if ($kelas_info['user_id'] == $user_id && time() - $data_kelas['timestamp'] <= 7200) { // 2 jam
                $kelas_session[] = $kelas_info;
            } else {
                unset($_SESSION['kelas_ditolak'][$kelas_id]); // Hapus jika lebih dari 2 jam
            }
        }
    }
}

// Gabungkan hasil akhir
$kelas = array_merge($kelas_aktif, $kelas_session);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $instruktur_id = $_POST['instruktur_id'];
    $rating = $_POST['rating'];

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
    <link rel="stylesheet" href="css/profile4.css">
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
                <li class="biasa-h"><a class="biasa" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="tentang-h"><a class="tentang" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main class="main">
        <!-- Layout Dua Kolom -->
        <div class="container">
            <!-- Kolom Kiri: Menu Navigasi -->
            <aside class="menu-container">
                <ul class="menu-profile">
                    <li class="menu-item active" data-target="#user-info">👤 User Info</li>
                    <li class="menu-item" data-target="#faq">❓ FAQ</li>
                    <li class="menu-item" data-target="#kelas-diikuti">📚 Kelas yang Diikuti</li>
                </ul>

                <!-- Log Out -->
                <a href="logout.php" class="log-out">Logout</a>
            </aside>

            <!-- Kolom Kanan: Konten Utama -->
            <section class="content-sections">
                <!-- User Info -->
                <div id="user-info" class="content-section active">
                    <div class="profile-section">
                        <div class="avatar">
                            <img src="foto/avatar.jpg" alt="Avatar">
                        </div>
                        <div class="user-details">
                            <h2><?= htmlspecialchars($user['nama']) ?></h2>
                            <p><?= htmlspecialchars($user['nama_lengkap']) ?></p>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>

                    <!-- Form Edit Profil -->
                    <form method="POST" action="update_profile.php" id="edit-profile-form">
                        <label for="nama">Username</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>

                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                        <label for="password">Password Baru (opsional)</label>
                        <input type="password" id="password" name="password" placeholder="********" minlength="6">
                        <i class="fas fa-eye-slash" onclick="togglePasswordVisibility(this)"></i>

                        <button type="submit">Simpan Perubahan</button>
                    </form>
                </div>

                <!-- FAQ -->
                <div id="faq" class="content-section">
                    
                        <div class="border-faq">
                            <p><strong>Siapa saja yang bisa ikut kursus ini?</strong></p>
                            <p>Kursus musik ini terbuka untuk semua kalangan, baik pemula maupun yang sudah pernah belajar musik sebelumnya. Anak-anak, remaja, maupun dewasa bisa mendaftar.</p>
                        </div>
                        <div class="border-faq">
                            <p><strong>Bagaimana sistem pertemuannya?</strong></p>
                            <p>Satu pertemuan berlangsung selama 1 jam, dengan jadwal seminggu sekali. Jadi dalam 1 bulan, peserta akan mengikuti 4 kali pertemuan untuk kelas yang dipilih.</p>
                        </div>
                        <div class="border-faq">
                            <p><strong>Apakah bisa memilih hari dan jam sendiri?</strong></p>
                            <p>Di awal, calon peserta hanya perlu membayar biaya pendaftaran dan tidak membayar biaya bulanan. Selanjutnya, pembayaran dilakukan setiap bulan berdasarkan kelas yang diikuti (4 pertemuan per bulan).</p>
                        </div>
                        <div class="border-faq">
                            <p><strong>Apa bisa ikut lebih dari 1 kelas?</strong></p>
                            <p>Bisa. Peserta dapat mendaftar lebih dari satu kelas selama jadwalnya tidak bentrok.</p>
                        </div>
                        <div class="border-faq">
                            <p><strong>Apakah ada sertifikat?</strong></p>
                            <p>Sertifikat akan diberikan kepada peserta yang telah mengikuti kursus minimal selama 6 bulan dan berhasil lulus ujian kelulusan. Ujian kelulusan diadakan satu kali dalam setahun, dan peserta akan dinilai berdasarkan keterampilan serta kemajuan belajarnya.</p>
                        </div>
                    
                </div>

                <!-- Kursus yang Diikuti -->
                <div id="kelas-diikuti" class="content-section">
                    <h2>Kelas yang Diikuti</h2>
                    <ul>
                        <?php if (empty($kelas)): ?>
                            <li>Tidak ada kelas yang diikuti.</li>
                        <?php else: ?>
                            <?php foreach ($kelas as $k): ?>
                                <li style="display: flex; justify-content: space-between; align-items: center;">
                                    <?php
                                    // Tampilkan informasi kelas
                                    if (isset($k['instruktur_nama'])):
                                    ?>
                                        <div class="kelas-info">
                                            <?= htmlspecialchars($k['instruktur_nama']) ?> - 
                                            <?= htmlspecialchars($k['hari']) ?> 
                                            <?= date('H:i', strtotime($k['jam'])) ?> 
                                            (Status: <?= htmlspecialchars($k['status']) ?>)
                                        </div>
                                    <?php
                                    elseif (isset($k['instruktur_id'])):
                                        // Ambil nama instruktur dari database
                                        $stmt = $pdo->prepare("SELECT nama FROM instruktur WHERE id = ?");
                                        $stmt->execute([$k['instruktur_id']]);
                                        $instruktur = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $instruktur_nama = $instruktur ? $instruktur['nama'] : 'Instruktur Tidak Diketahui';
                                    ?>
                                        <div class="kelas-info">
                                            <?= htmlspecialchars($instruktur_nama) ?> - 
                                            <?= htmlspecialchars($k['hari']) ?> 
                                            <?= date('H:i', strtotime($k['jam'])) ?> 
                                            (Status: Ditolak)
                                        </div>
                                    <?php endif; ?>
                                    <!-- Tombol Konfirmasi Pembayaran -->
                                    <?php if ($k['status'] === 'pending'): ?>
                                        <a href="pembayaran.php?kelas_id=<?= $k['id'] ?>" class="btn-konfirmasi">Konfirmasi Pembayaran</a>
                                    <?php endif; ?>
                                </li>
                                <?php if ($k['status'] === 'disetujui'): ?>
                                    <?php

                                    $stmt = $pdo->prepare("SELECT * FROM rating_instruktur WHERE user_id = ? AND instruktur_id = ?");
                                    $stmt->execute([$user_id, $k['instruktur_id']]);
                                    $has_rated = $stmt->rowCount() > 0;
                                    ?>
                                    <?php if (!$has_rated): ?>
                                        <form method="POST" class="rating-form" data-instruktur-id="<?= $k['instruktur_id'] ?>">
                                            <input type="hidden" name="instruktur_id" value="<?= $k['instruktur_id'] ?>">
                                            <div class="star-rating">
                                                <input type="radio" id="star5" name="rating" value="5"><label for="star5">&#9733;</label>
                                                <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
                                                <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
                                                <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
                                                <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
                                            </div>
                                            <button class="btn-rating" type="submit">Kirim Rating</button>
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
                </div>
            </section>
        </div>
    </main>

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

        <!-- Custom Popup dengan Overlay -->
        <div id="custom-popup" class="popup hidden">
            <div class="popup-content">
                <span class="close-btn" onclick="hidePopup()">&times;</span>
                <p id="popup-message"></p>
                <button class="btn-close" onclick="hidePopup()">Tutup</button>
            </div>
        </div>

        <div id="popup-overlay" class="overlay hidden" onclick="hidePopup()"></div>
        
        <!-- Custom Rating Success Popup -->
        <div id="rating-success-popup" class="popup-rating hidden">
            <div class="popup-rating-content">
                <span class="close-btn" onclick="hideRatingPopup()">&times;</span>
                <p id="rating-popup-message">Rating berhasil disimpan!</p>
                <button class="btn-close" onclick="hideRatingPopup()">Tutup</button>
            </div>
        </div>

        <div id="rating-popup-overlay" class="overlay-rating hidden" onclick="hideRatingPopup()"></div>

        <script>
 
        document.querySelectorAll('.menu-profile .menu-item').forEach(item => {
            item.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const currentActive = document.querySelector('.content-section.active');
                const newActive = document.querySelector(targetId);


                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });


                newActive.classList.add('active');


                document.querySelectorAll('.menu-item').forEach(menuItem => {
                    menuItem.classList.remove('active');
                });

  
                this.classList.add('active');
            });
        });

        // Toggle password visibility
        function togglePasswordVisibility(icon) {
            const passwordInput = icon.previousElementSibling;
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            }
        }

        function showPopup(message) {
            document.getElementById("popup-message").innerText = message;
            document.getElementById("custom-popup").classList.remove("hidden");
            document.getElementById("popup-overlay").classList.remove("hidden");
        }

        function hidePopup() {
            document.getElementById("custom-popup").classList.add("hidden");
            document.getElementById("popup-overlay").classList.add("hidden");
        }

        
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                hidePopup();
            }
        });

        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showPopup(data.message);
       
                    document.querySelector('h2').innerText = formData.get('nama');
                    document.querySelector('p:nth-of-type(2)').innerText = formData.get('email');
                } else {
                    showPopup(data.message);
                }
            })
            .catch(error => {
                showPopup("Terjadi kesalahan saat menyimpan perubahan.");
                console.error('Error:', error);
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            if (window.location.hash === "#faq") {
                document.querySelector('[data-target="#faq"]').click();
            }
        });

        function showRatingPopup(message) {
            document.getElementById("rating-popup-message").innerText = message;
            document.getElementById("rating-success-popup").classList.remove("hidden");
            document.getElementById("rating-popup-overlay").classList.remove("hidden");
        }

        function hideRatingPopup() {
            document.getElementById("rating-success-popup").classList.add("hidden");
            document.getElementById("rating-popup-overlay").classList.add("hidden");
        }

        document.querySelectorAll('.rating-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {

                    if (data.includes('Rating berhasil disimpan')) {
                        showRatingPopup("Rating berhasil disimpan!");
                        // Optional: reload halaman setelah 3 detik untuk refresh data
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        alert("Gagal menyimpan rating.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Terjadi kesalahan saat mengirim rating.");
                });
            });
        });

        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
    </script>
</body>
</html>