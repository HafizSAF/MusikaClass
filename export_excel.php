<?php
require 'vendor/autoload.php';
require 'includes/config.php'; // Pastikan koneksi database tersedia

$bulanIndo = [
    'January'   => 'Januari',
    'February'  => 'Februari',
    'March'     => 'Maret',
    'April'     => 'April',
    'May'       => 'Mei',
    'June'      => 'Juni',
    'July'      => 'Juli',
    'August'    => 'Agustus',
    'September' => 'September',
    'October'   => 'Oktober',
    'November'  => 'November',
    'December'  => 'Desember'
];

date_default_timezone_set('Asia/Jakarta');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data yang sudah disetujui
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

// Cek apakah ada data
if (empty($approved_kelas)) {
    die("Tidak ada data yang bisa di-export.");
}

// Buat objek Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Tabel
$headers = ['ID', 'Nama Lengkap', 'Pendidikan', 'Nomor HP', 'Instruktur', 'Hari', 'Jam', 'Status'];
$sheet->fromArray($headers, null, 'A1');

// Isi Data
$rowNumber = 2;
foreach ($approved_kelas as $row) {
    $sheet->setCellValue('A' . $rowNumber, $row['kelas_id']);
    $sheet->setCellValue('B' . $rowNumber, $row['nama_lengkap']);
    $sheet->setCellValue('C' . $rowNumber, $row['pendidikan']);
    $sheet->setCellValue('D' . $rowNumber, $row['nomor_hp']);
    $sheet->setCellValue('E' . $rowNumber, $row['instruktur_nama']);
    $sheet->setCellValue('F' . $rowNumber, $row['hari']);
    $sheet->setCellValue('G' . $rowNumber, $row['jam']);
    $sheet->setCellValue('H' . $rowNumber, $row['status']);
    $rowNumber++;
}

// Format tanggal dalam bahasa Indonesia
$bulanInggris = date("F");
$tanggal = date("d") . " " . $bulanIndo[$bulanInggris] . " " . date("Y");

// Header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data-terdaftar-'.$tanggal.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;