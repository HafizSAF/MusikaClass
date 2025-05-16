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

// Query untuk data instruktur
$query_instruktur = "
    SELECT 
        id,
        nama,
        kelas,
        harga_kelas,
        rating
    FROM instruktur
";
$instruktur = $pdo->query($query_instruktur)->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Setujui pendaftaran kelas
        $stmt = $pdo->prepare("UPDATE kelas SET status = 'disetujui' WHERE id = ?");
        $stmt->execute([$id]);

    } elseif ($action == 'reject') {
        $pesan_penolakan = trim($_POST['pesan_penolakan'] ?? '');

        // Ambil data kelas sebelum ditolak
        $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$id]);
        $kelas = $stmt->fetch(PDO::FETCH_ASSOC);

        // Simpan ke session
        if (!isset($_SESSION['kelas_ditolak'])) {
            $_SESSION['kelas_ditolak'] = [];
        }
        $kelas['timestamp'] = time();
        $_SESSION['kelas_ditolak'][$id] = $kelas;

        // Update status dan pesan
        $stmt = $pdo->prepare("UPDATE kelas SET status = 'ditolak', pesan_penolakan = ?, deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$pesan_penolakan, $id]);

    } elseif ($action == 'delete') {
        // Tandai kelas sebagai dihapus
        $stmt = $pdo->prepare("UPDATE kelas SET status = 'dihapus', deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);

    } elseif ($action == 'delete_instructor') {
        // Hapus instruktur dari database
        $stmt = $pdo->prepare("DELETE FROM instruktur WHERE id = ?");
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
    <link rel="stylesheet" href="css/admin5.css">
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
                                <button type="button" class="btn-delete" data-id="<?= $k['kelas_id'] ?>" data-action="delete">Hapus</button>
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
                    <th>Harga Kelas</th>
                    <th>Rating</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($instruktur as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['nama']) ?></td>
                        <td><?= htmlspecialchars($i['kelas']) ?></td>
                        <td>Rp. <?= number_format($i['harga_kelas'], 2) ?></td>
                        <td><?= number_format($i['rating'], 2) ?>/5</td>
                        <td>
                            <button type="button" class="btn-delete-instruktur" data-id="<?= $i['id'] ?>" data-action="delete_instruktur">Hapus</button>
                            <form id="form-delete-instruktur-<?= $i['id'] ?>" method="POST" style="display:none;">
                                <input type="hidden" name="id" value="<?= $i['id'] ?>">
                                <input type="hidden" name="action" value="delete_instructor">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>

    <!-- Modal Konfirmasi Custom -->
    <div id="notif-confirm" class="notif">
        <div class="notif-content">
            <span id="close-notif">&times;</span>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <button id="btn-ya">Ya</button>
            <button id="btn-tidak">Tidak</button>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Instruktur -->
    <div id="notif-confirm-instruktur" class="notif">
        <div class="notif-content">
            <span id="close-notif-instruktur">&times;</span>
            <p>Apakah Anda yakin ingin menghapus instruktur ini?</p>
            <button id="btn-ya-instruktur">Ya</button>
            <button id="btn-tidak-instruktur">Tidak</button>
        </div>
    </div>
    <script>               
        let currentFormKelas = null;
        let currentFormInstruktur = null;

        // Tombol hapus kelas
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                currentFormKelas = document.getElementById('form-delete-' + id);
                document.getElementById('notif-confirm').style.display = 'block';
            });
        });

        // Tombol hapus instruktur
        document.querySelectorAll('.btn-delete-instruktur').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                currentFormInstruktur = document.getElementById('form-delete-instruktur-' + id);
                document.getElementById('notif-confirm-instruktur').style.display = 'block';
            });
        });

        // Tombol Ya - Hapus Kelas
        document.getElementById('btn-ya').addEventListener('click', function () {
            if (currentFormKelas) {
                currentFormKelas.submit();
            }
        });

        // Tombol Ya - Hapus Instruktur
        document.getElementById('btn-ya-instruktur').addEventListener('click', function () {
            if (currentFormInstruktur) {
                currentFormInstruktur.submit();
            }
        });

        // Tutup notifikasi kelas
        function closeNotifKelas() {
            document.getElementById('notif-confirm').style.display = 'none';
            currentFormKelas = null;
        }

        // Tutup notifikasi instruktur
        function closeNotifInstruktur() {
            document.getElementById('notif-confirm-instruktur').style.display = 'none';
            currentFormInstruktur = null;
        }

        // Tombol Tidak / Close
        document.getElementById('btn-tidak').addEventListener('click', closeNotifKelas);
        document.getElementById('close-notif').addEventListener('click', closeNotifKelas);

        document.getElementById('btn-tidak-instruktur').addEventListener('click', closeNotifInstruktur);
        document.getElementById('close-notif-instruktur').addEventListener('click', closeNotifInstruktur);

        // Tutup notif jika klik di luar konten
        window.addEventListener('click', function (event) {
            const notifKelas = document.getElementById('notif-confirm');
            const notifInstruktur = document.getElementById('notif-confirm-instruktur');
            if (event.target === notifKelas) closeNotifKelas();
            if (event.target === notifInstruktur) closeNotifInstruktur();
        });
    </script>
</body>
</html>