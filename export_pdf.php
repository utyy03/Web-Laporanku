<?php
session_start();
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include('config.php');

// Pastikan session memiliki data yang diperlukan
if (!isset($_SESSION['selected_ids'])) {
    die("Data tidak ditemukan. Silakan pilih data pengeluaran terlebih dahulu.");
}

// Ambil data dari session
$selected_ids = $_SESSION['selected_ids'];
$nama_penandatangan = $_SESSION['sign_name'] ?? "Nama Penandatangan";
$jabatan_penandatangan = $_SESSION['sign_position'] ?? "Jabatan Penandatangan";
$nik_penandatangan = $_SESSION['sign_nik'] ?? "NIK Penandatangan";
$tanggal_laporan = date("d F Y");

// Query data pengeluaran berdasarkan ID yang dipilih
$ids = implode(",", array_map('intval', $selected_ids));
$query = "SELECT * FROM pengeluaran WHERE id IN ($ids)";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Data tidak ditemukan. Silakan pilih data pengeluaran terlebih dahulu.");
}

// Ambil judul utama, sub judul, bulan, dan tahun dari salah satu record pengeluaran yang dipilih
$row = $result->fetch_assoc();
$judul_utama = strtoupper($row['judul_utama']);
$sub_judul = strtoupper($row['sub_judul']);
$bulan = strtoupper($row['bulan']);
$tahun = $row['tahun'];

// Reset hasil query agar bisa dipakai lagi
$result->data_seek(0);

// HTML untuk konten PDF
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .title { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .subtitle { text-align: center; font-size: 14px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .right-align { text-align: right; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>

<div class="title">' . htmlspecialchars($judul_utama) . '</div>
<div class="title">' . htmlspecialchars($sub_judul) . '</div>
<div class="title">BULAN ' . htmlspecialchars($bulan) . ' ' . htmlspecialchars($tahun) . '</div>

<table>
    <tr>
        <th>NO</th>
        <th>Akun</th>
        <th>DESKRIPSI</th>
        <th>TANGGAL</th>
        <th>DESKRIPSI KEGIATAN</th>
        <th>REALISASI (RP)</th>
        <th>Devisi</th>
    </tr>';

$no = 1;
$total_realisasi = 0;

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row['akun']) . '</td>
                <td>' . htmlspecialchars($row['deskripsi']) . '</td>
                <td>' . date("d/m/Y", strtotime($row['tanggal'])) . '</td>
                <td>' . htmlspecialchars($row['deskripsi_kegiatan']) . '</td>
                <td class="right-align">' . number_format($row['realisasi'], 0, ',', '.') . '</td>
                <td>' . htmlspecialchars($row['devisi']) . '</td>
              </tr>';
    $total_realisasi += $row['realisasi'];
}

$html .= '<tr>
            <td colspan="5" class="right-align"><strong>TOTAL REALISASI</strong></td>
            <td class="right-align"><strong>' . number_format($total_realisasi, 0, ',', '.') . '</strong></td>
            <td></td>
          </tr>
</table>

<div class="footer">
    <p>Bogor, ' . $tanggal_laporan . '</p>
    <p>' . htmlspecialchars($jabatan_penandatangan) . '</p>
    <br><br><br>
    <p>' . htmlspecialchars($nama_penandatangan) . '</p>
    <p>NIK: ' . htmlspecialchars($nik_penandatangan) . '</p>
</div>

</body>
</html>';

// Inisialisasi DOMPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');
$dompdf->loadHtml($html);
$dompdf->render();

// Output sebagai PDF
$dompdf->stream("Laporan_Pengeluaran.pdf", ["Attachment" => false]);
exit;
?>
