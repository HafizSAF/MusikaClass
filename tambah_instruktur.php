<?php
session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];

    try {
        $stmt = $pdo->prepare("INSERT INTO instruktur (nama, kelas, rating) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $kelas, 0.00]);
        echo "<script>alert('Instruktur berhasil ditambahkan!'); window.location='admin.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Instruktur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tambah-instruktur.css">
</head>
<body>
    <header>
        <h1>Tambah Instruktur</h1>
        <p><a href="admin.php">Kembali ke Dashboard Admin</a></p>
    </header>

    <main>
        <form method="POST">
            <label for="nama">Nama Instruktur:</label>
            <input type="text" name="nama" id="nama" required><br>

            <label for="kelas">Kelas yang Diajar:</label>
            <select name="kelas" id="kelas" required>
                <option value="vocal">Vocal</option>
                <option value="gitar elektrik">Gitar Elektrik</option>
                <option value="drum">Drum</option>
                <option value="keyboard">Keyboard</option>
            </select><br>

            <button type="submit">Tambah Instruktur</button>
        </form>
    </main>
</body>
</html>