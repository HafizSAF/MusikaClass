<?php
include 'includes/config.php';

session_start();

// Periksa apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    // Jika sudah login, arahkan ke halaman beranda atau admin
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Simpan session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $showPopup = true; // Tampilkan popup jika salah
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MusikaClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <style>
        /* Style untuk popup */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999;
        }

        .popup-content {
            background-color: #ffffff;
            margin: 15% auto;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #ccc;
            width: 320px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        .close-btn {
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 15px;
        }

        .close-btn:hover {
            color: black;
        }

        .btn-close {
            margin-top: 15px;
            padding: 8px 16px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-close:hover {
            background-color: #e60000;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>

<body>
    <!-- Popup Notifikasi -->
    <div id="errorPopup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <p>Email atau password salah!</p>
            <button class="btn-close" onclick="closePopup()">Tutup</button>
        </div>
    </div>

    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Musika<span>Class</a>
            </div>
            <ul class="menu">
                <li class="biasa-h"><a class="biasa" href="index.php" id="home-link">Beranda</a></li>
                <li class="biasa-h"><a class="biasa" href="tentang.php">Tentang Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="daftar_kelas.php">Daftar Kursus</a></li>
                <li class="biasa-h"><a class="biasa" href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="login-form">
            <div class="kolom">
                <form class="POST" method="POST">
                    Email: <input type="email" name="email" required><br>
                    Password: <input type="password" name="password" required><br>
                    <button type="submit">Login</button>
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
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

    <script>
        function closePopup() {
            document.getElementById("errorPopup").style.display = "none";
        }

        // Klik di luar popup untuk menutup
        window.onclick = function(event) {
            var popup = document.getElementById("errorPopup");
            if (event.target == popup) {
                popup.style.display = "none";
            }
        }

        // Tampilkan popup jika login gagal
        <?php if ($showPopup): ?>
            window.onload = function() {
                document.getElementById("errorPopup").style.display = "block";
            };
        <?php endif; ?>
    </script>
</body>
</html>