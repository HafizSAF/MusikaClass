<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validasi panjang minimal
    if (strlen($nama) < 3) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('notificationModal');
                var modalMessage = document.getElementById('modalMessage');
                modalMessage.textContent = 'Username minimal 3 karakter!';
                modal.style.display = 'block';
                setTimeout(function() { modal.style.display = 'none'; }, 3000);
            });
        </script>";
        exit;
    }

    if (strlen($password) < 6) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('notificationModal');
                var modalMessage = document.getElementById('modalMessage');
                modalMessage.textContent = 'Password minimal 6 karakter!';
                modal.style.display = 'block';
                setTimeout(function() { modal.style.display = 'none'; }, 3000);
            });
        </script>";
        exit;
    }

    // Cek apakah nama atau email sudah ada
    $stmt = $pdo->prepare("SELECT nama, email FROM users WHERE nama = ? OR email = ?");
    $stmt->execute([$nama, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $errors = [];

    if ($user) {
        if ($user['nama'] === $nama) {
            $errors[] = "Username sudah digunakan!";
        }
        if ($user['email'] === $email) {
            $errors[] = "Email sudah terdaftar!";
        }
    }

    if (!empty($errors)) {
        $errorMessages = implode(" ", $errors);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('notificationModal');
                var modalMessage = document.getElementById('modalMessage');
                modalMessage.textContent = '$errorMessages';
                modal.style.display = 'block';
                setTimeout(function() { modal.style.display = 'none'; }, 4000);
            });
        </script>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$nama, $email, $password]);

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('notificationModal');
                    var modalMessage = document.getElementById('modalMessage');
                    modalMessage.textContent = 'Pendaftaran berhasil!';
                    modal.classList.add('success');
                    modal.style.display = 'block';
                    
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 1500);
                });
            </script>";
        } catch (PDOException $e) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('notificationModal');
                    var modalMessage = document.getElementById('modalMessage');
                    modalMessage.textContent = 'Terjadi kesalahan: " . addslashes($e->getMessage()) . "';
                    modal.style.display = 'block';
                    setTimeout(function() { modal.style.display = 'none'; }, 4000);
                });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register3.css">
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
                <li class="biasa-h"><a class="biasa" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="register-form">
            <div class="form-container">
                <h2>Daftar Akun</h2>
                <form method="POST">
                    <label for="nama">Username</label>
                    <input type="text" id="nama" name="nama" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="6">

                    <button type="submit">Daftar</button>
                    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </form>
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

    <!-- Modal Popup -->
    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <style>
        /* Style untuk modal */
        .modal {
            display: none; /* Sembunyikan awalnya */
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 15px;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        // Script untuk tombol close
        document.querySelector('.close-btn').addEventListener('click', function() {
            document.getElementById('notificationModal').style.display = 'none';
        });

        // Optional: Tutup popup jika klik di luar konten
        window.onclick = function(event) {
            var modal = document.getElementById('notificationModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
    </script>
</body>
</html>