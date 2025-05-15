<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['nama']); // username berasal dari input "nama"
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Sanitasi
    $username = htmlspecialchars($username);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? htmlspecialchars($email) : null;

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Email tidak valid.']);
        exit;
    }

    // Cek apakah email sudah digunakan oleh user lain
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan!']);
        exit;
    }

    // Cek apakah username sudah digunakan oleh user lain
    $stmt = $pdo->prepare("SELECT id FROM users WHERE nama = ? AND id != ?");
    $stmt->execute([$username, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username tidak tersedia!']);
        exit;
    }

    // Update data pengguna
    $sql = "UPDATE users SET nama = ?, email = ?";
    $params = [$username, $email];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $hashed_password;
    }

    $sql .= " WHERE id = ?";
    $params[] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Simpan ke session
        $_SESSION['nama'] = $username;
        $_SESSION['email'] = $email;

        echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diperbarui!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan perubahan.']);
    }
}
?>