<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

// Periksa apakah kelas sudah dipilih
if (!isset($_SESSION['kelas'])) {
    // Jika kelas belum dipilih, arahkan ke halaman daftar kelas
    header("Location: daftar_kelas.php");
    exit;
}

include 'includes/config.php';

$user_id = $_SESSION['user_id'];

// Periksa apakah data formulir sudah diisi
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($user['nama_lengkap']) || empty($user['pendidikan']) || empty($user['nomor_hp'])) {
    // Jika data belum lengkap, arahkan ke halaman pengisian data
    header("Location: daftar_data.php");
    exit;
}

$selected_kelas = $_SESSION['kelas'];

// Ambil instruktur yang mengajar kelas terpilih
$query = "
    SELECT id AS instruktur_id, nama AS instruktur_nama 
    FROM instruktur 
    WHERE kelas = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$selected_kelas]);
$instruktur_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pilih Instruktur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/pilihinstruktur.css">
</head>
<body>
    <header>
        <h1>Pilih Instruktur untuk Kelas: <?= htmlspecialchars($_SESSION['kelas']) ?></h1>
        <p><a href="daftar_kelas.php">Kembali ke Daftar Kelas</a></p>
    </header>

    <main>
        <form method="POST" action="proses_daftar.php">
            <label for="instruktur_id">Pilih Instruktur:</label>
            <select name="instruktur_id" id="instruktur_id" required>
                <?php if (empty($instruktur_list)): ?>
                    <option value="">Tidak ada instruktur tersedia</option>
                <?php else: ?>
                    <?php foreach ($instruktur_list as $instruktur): ?>
                        <option value="<?= $instruktur['instruktur_id'] ?>"><?= htmlspecialchars($instruktur['instruktur_nama']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select><br>

            <label for="hari">Pilih Hari:</label>
            <select name="hari" id="hari" required>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
            </select><br>

            <label for="jam">Pilih Jam:</label>
            <select name="jam" id="jam" required>
                <option value="11:00:00">11:00</option>
                <option value="12:00:00">12:00</option>
                <option value="13:00:00">13:00</option>
                <option value="14:00:00">14:00</option>
                <option value="15:00:00">15:00</option>
                <option value="16:00:00">16:00</option>
            </select><br>

            <button type="submit">Daftar Sekarang</button>
        </form>
    </main>

    <!-- Modal Notifikasi -->
    <div class="modal-overlay" id="errorModal">
        <div class="modal-box">
            <span class="close-btn" onclick="closeModal()">×</span>
            <h3>Peringatan</h3>
            <p id="modalMessage"></p>
            <button onclick="closeModal()">Tutup</button>
        </div>
    </div>

    <script>
        function showModal(message) {
            document.getElementById("modalMessage").textContent = message;
            document.getElementById("errorModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("errorModal").style.display = "none";
        }

        // Jika ada pesan error dari session, tampilkan modal
        <?php if (isset($_SESSION['error_message'])): ?>
            window.onload = function() {
                showModal("<?=$_SESSION['error_message']?>");
                <?php unset($_SESSION['error_message']); // hapus setelah ditampilkan ?>
            };
        <?php endif; ?>
    </script>
</body>
</html>