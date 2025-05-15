<?php
session_start();
include 'includes/config.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Query untuk pendaftaran yang membutuhkan konfirmasi
$query_pending = "
    SELECT 
        k.id AS kelas_id,
        u.nama_lengkap,
        u.pendidikan,
        u.nomor_hp,
        i.nama AS instruktur_nama,
        k.hari,
        k.jam,
        k.status
    FROM kelas k
    LEFT JOIN users u ON k.user_id = u.id
    LEFT JOIN instruktur i ON k.instruktur_id = i.id
    WHERE k.deleted_at IS NULL AND k.status = 'pending'
    ORDER BY k.hari, k.jam
";
$pending_kelas = $pdo->query($query_pending)->fetchAll(PDO::FETCH_ASSOC);

// Query untuk data yang sudah terdaftar
$query_approved = "
    SELECT 
        k.id AS kelas_id,
        u.nama_lengkap,
        u.pendidikan,
        u.nomor_hp,
        i.nama AS instruktur_nama,
        k.hari,
        k.jam,
        k.status
    FROM kelas k
    LEFT JOIN users u ON k.user_id = u.id
    LEFT JOIN instruktur i ON k.instruktur_id = i.id
    WHERE k.deleted_at IS NULL AND k.status = 'disetujui'
    ORDER BY k.hari, k.jam
";
$approved_kelas = $pdo->query($query_approved)->fetchAll(PDO::FETCH_ASSOC);

// Ambil data instruktur
$instruktur = $pdo->query("SELECT * FROM instruktur")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML tetap sama -->

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $pesan_penolakan = isset($_POST['pesan_penolakan']) ? trim($_POST['pesan_penolakan']) : null;

    if ($action == 'approve') {
        // Setujui pendaftaran
        $stmt = $pdo->prepare("UPDATE kelas SET status = 'disetujui' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action == 'reject') {
        // Ambil data kelas sebelum dihapus
        $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$id]);
        $kelas = $stmt->fetch(PDO::FETCH_ASSOC);

        // Simpan ke session dengan timestamp
        if (!isset($_SESSION['kelas_ditolak'])) {
            $_SESSION['kelas_ditolak'] = [];
        }

        // Tambahkan kelas yang ditolak ke session
        $kelas['timestamp'] = time(); // Tambahkan waktu penolakan
        $_SESSION['kelas_ditolak'][$id] = $kelas;

        // Update status dan deleted_at
        $stmt = $pdo->prepare("UPDATE kelas SET status = 'ditolak', pesan_penolakan = ?, deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$pesan_penolakan, $id]);

    } elseif ($action == 'delete') {
        // Hapus data kelas (tandai sebagai dihapus)
        $stmt = $pdo->prepare("UPDATE kelas SET status = 'dihapus', deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin3.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <p><a href="logout.php">Logout</a></p>
    </header>

    <main>
        <!-- Daftar Pendaftaran yang Membutuhkan Konfirmasi -->
        <section>
            <h2>Pendaftaran yang Membutuhkan Konfirmasi</h2>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Pendidikan</th>
                    <th>Nomor HP</th>
                    <th>Instruktur</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php if (empty($pending_kelas)): ?>
                    <tr>
                        <td colspan="9">Tidak ada pendaftaran yang membutuhkan konfirmasi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_kelas as $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($k['kelas_id']) ?></td>
                            <td><?= htmlspecialchars($k['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($k['pendidikan']) ?></td>
                            <td><?= htmlspecialchars($k['nomor_hp']) ?></td>
                            <td><?= htmlspecialchars($k['instruktur_nama']) ?></td>
                            <td><?= htmlspecialchars($k['hari']) ?></td>
                            <td><?= date('H:i', strtotime($k['jam'])) ?></td>
                            <td><?= htmlspecialchars($k['status']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $k['kelas_id'] ?>">
                                    <button type="submit" name="action" value="approve">Setujui</button>
                                    <button type="submit" name="action" value="reject">Tolak</button>
                                    <input type="text" name="pesan_penolakan" placeholder="Pesan penolakan (opsional)">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </section>

        <!-- Daftar Data yang Sudah Terdaftar -->
        <section>
            <h2>Data yang Sudah Terdaftar</h2>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Pendidikan</th>
                    <th>Nomor HP</th>
                    <th>Instruktur</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php if (empty($approved_kelas)): ?>
                    <tr>
                        <td colspan="9">Tidak ada data yang sudah terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($approved_kelas as $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($k['kelas_id']) ?></td>
                            <td><?= htmlspecialchars($k['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($k['pendidikan']) ?></td>
                            <td><?= htmlspecialchars($k['nomor_hp']) ?></td>
                            <td><?= htmlspecialchars($k['instruktur_nama']) ?></td>
                            <td><?= htmlspecialchars($k['hari']) ?></td>
                            <td><?= date('H:i', strtotime($k['jam'])) ?></td>
                            <td><?= htmlspecialchars($k['status']) ?></td>
                            <td>
                                <button type="button" class="btn-delete" data-id="<?= $k['kelas_id'] ?>">Hapus</button>
                                <form id="form-delete-<?= $k['kelas_id'] ?>" method="POST" style="display:none;">
                                    <input type="hidden" name="id" value="<?= $k['kelas_id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>

            <a href="export_excel.php" class="btn-download">⬇️ Download Excel</a>
        </section>

        <!-- Daftar Instruktur -->
        <section>
            <h2>Daftar Instruktur</h2>
            <p><a class="tmbh-instruktur" href="tambah_instruktur.php">Tambah Instruktur</a></p>
            <table border="1">
                <tr>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Rating</th>
                </tr>
                <?php foreach ($instruktur as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['nama']) ?></td>
                        <td><?= htmlspecialchars($i['kelas']) ?></td>
                        <td><?= number_format($i['rating'], 2) ?>/5</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>

    <!-- Modal Konfirmasi Custom -->
    <div id="modal-confirm" class="modal">
        <div class="modal-content">
            <span id="close-modal">&times;</span>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <button id="btn-ya">Ya</button>
            <button id="btn-tidak">Tidak</button>
        </div>
    </div>

    <style>
        /* Style untuk modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
            position: relative;
        }
        #close-modal {
            position: absolute;
            top: 10px; right: 15px;
            font-size: 20px;
            cursor: pointer;
        }
        .modal-content button {
            margin: 10px 10px 0 0;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }
        #btn-ya {
            background-color: #d9534f;
            color: white;
            border: none;
        }
        #btn-tidak {
            background-color: #ccc;
            color: black;
            border: none;
        }
    </style>
    
    <script>
        let currentForm = null;

        // Saat tombol hapus diklik
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                currentForm = document.getElementById('form-delete-' + id);
                document.getElementById('modal-confirm').style.display = 'block';
            });
        });

        // Tombol Ya
        document.getElementById('btn-ya').addEventListener('click', function () {
            if (currentForm) {
                currentForm.submit();
            }
        });

        // Tombol Tidak / Close
        document.getElementById('btn-tidak').addEventListener('click', closeModal);
        document.getElementById('close-modal').addEventListener('click', closeModal);

        function closeModal() {
            document.getElementById('modal-confirm').style.display = 'none';
            currentForm = null;
        }

        // Tutup modal jika klik di luar area konten
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('modal-confirm');
            if (event.target === modal) {
                closeModal();
            }
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $action = $_POST['action'];
        $pesan_penolakan = isset($_POST['pesan_penolakan']) ? trim($_POST['pesan_penolakan']) : null;

        if ($action == 'approve') {
            // Setujui pendaftaran
            $stmt = $pdo->prepare("UPDATE kelas SET status = 'disetujui' WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($action == 'reject') {
            // Tolak pendaftaran, simpan pesan penolakan, dan isi kolom deleted_at
            $stmt = $pdo->prepare("UPDATE kelas SET status = 'ditolak', pesan_penolakan = ?, deleted_at = NOW() WHERE id = ?");
            $stmt->execute([$pesan_penolakan, $id]);
        } elseif ($action == 'delete') {
            // Hapus data kelas (tandai sebagai dihapus)
            $stmt = $pdo->prepare("UPDATE kelas SET status = 'dihapus', deleted_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: admin.php");
        exit;
    }
    ?>
</body>
</html>