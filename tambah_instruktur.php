<?php
session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    $harga_kelas = floatval($_POST['harga_kelas']);

    if (empty($nama) || empty($kelas) || empty($harga_kelas)) {
        echo "<script>
                window.onload = () => {
                    showNotif('Mohon lengkapi semua field!', 'error');
                };
              </script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO instruktur (nama, kelas, harga_kelas, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $kelas, $harga_kelas, 0.00]);

        echo "<script>
                window.onload = () => {
                    showNotif('Instruktur berhasil ditambahkan!', 'success');
                    setTimeout(() => {
                        window.location.href = 'admin.php';
                    }, 1500);
                };
              </script>";

    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "<script>
                window.onload = () => {
                    showNotif('Gagal menambahkan instruktur: " . addslashes($e->getMessage()) . "', 'error');
                };
              </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Instruktur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins :wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tambah_instruktur.css">
</head>
<body>
    <header>
        <h1>Tambah Instruktur</h1>
        <p><a href="admin.php">Kembali ke Dashboard Admin</a></p>
    </header>

    <!-- Notifikasi Popup -->
    <div id="notif" class="notif hidden">
        <span id="notif-message"></span>
        <button onclick="hideNotif()" class="close-btn">&times;</button>
    </div>

    <main>
        <form method="POST">
            <label for="nama">Nama Instruktur:</label>
            <input type="text" name="nama" id="nama" required><br>

            <label for="kelas">Kelas yang Diajar:</label>
            <select name="kelas" id="kelas" required>
                <option value="Vocal">Vocal</option>
                <option value="Gitar Elektrik">Gitar Elektrik</option>
                <option value="Drum">Drum</option>
                <option value="Keyboard">Keyboard</option>
            </select><br>

            <label for="harga_kelas">Harga Kelas:</label>
            <input type="number" step="0.01" name="harga_kelas" id="harga_kelas" required><br>

            <button type="submit">Tambah Instruktur</button>
        </form>
    </main>

    <script>
        function showNotif(message, type = 'success') {
            const notif = document.getElementById('notif');
            const msgSpan = document.getElementById('notif-message');

            // Set pesan dan jenis notifikasi
            msgSpan.textContent = message;
            notif.className = 'notif show ' + type;

            // Sembunyikan otomatis setelah 3 detik
            setTimeout(() => {
                hideNotif();
            }, 3000);
        }

        function hideNotif() {
            const notif = document.getElementById('notif');
            notif.classList.add('hidden');
            notif.classList.remove('show', 'success', 'error');
        }
    </script>

</body>
</html>