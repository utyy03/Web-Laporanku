<?php
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

include('config.php');

// Debugging untuk memastikan session memiliki data yang diperlukan
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

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul laporan
$sheet->setCellValue('A1', $judul_utama);
$sheet->setCellValue('A2', $sub_judul);
$sheet->setCellValue('A3', 'BULAN ' . $bulan . ' ' . $tahun);

// Style untuk header judul
$sheet->mergeCells('A1:G1');
$sheet->mergeCells('A2:G2');
$sheet->mergeCells('A3:G3');
$sheet->getStyle('A1:A3')->getFont()->setBold(true);
$sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Set header tabel
$sheet->setCellValue('A5', 'NO');
$sheet->setCellValue('B5', 'Akun');
$sheet->setCellValue('C5', 'DESKRIPSI');
$sheet->setCellValue('D5', 'TANGGAL');
$sheet->setCellValue('E5', 'DESKRIPSI KEGIATAN');
$sheet->setCellValue('F5', 'REALISASI (RP)');
$sheet->setCellValue('G5', 'Devisi');

// Style untuk header tabel
$sheet->getStyle('A5:G5')->getFont()->setBold(true);
$sheet->getStyle('A5:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A5:G5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Isi data pengeluaran
$rowNumber = 6;
$no = 1;
$total_realisasi = 0;

while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['akun']);
    $sheet->setCellValue('C' . $rowNumber, $row['deskripsi']);
    $sheet->setCellValue('D' . $rowNumber, date("d/m/Y", strtotime($row['tanggal'])));
    $sheet->setCellValue('E' . $rowNumber, $row['deskripsi_kegiatan']);
    $sheet->setCellValue('F' . $rowNumber, $row['realisasi']);
    $sheet->setCellValue('G' . $rowNumber, $row['devisi']);
    $total_realisasi += $row['realisasi'];
    $rowNumber++;
}

// Tambahkan baris total realisasi
$sheet->setCellValue('E' . $rowNumber, 'TOTAL REALISASI');
$sheet->setCellValue('F' . $rowNumber, $total_realisasi);
$sheet->getStyle('E' . $rowNumber)->getFont()->setBold(true);
$sheet->getStyle('F' . $rowNumber)->getFont()->setBold(true);

// Terapkan border untuk seluruh area tabel, termasuk baris total
$startRow = 5; // Baris awal header tabel
$endRow = $rowNumber; // Baris akhir dari data atau total

$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle("A{$startRow}:G{$endRow}")->applyFromArray($styleArray);

// Tambahkan bagian tanda tangan
$rowNumber += 3;
$sheet->setCellValue('F' . $rowNumber, 'Bogor, ' . $tanggal_laporan);
$rowNumber++;
$sheet->setCellValue('F' . $rowNumber, $jabatan_penandatangan);
$rowNumber += 3;
$sheet->setCellValue('F' . $rowNumber, $nama_penandatangan);
$rowNumber++;
$sheet->setCellValue('F' . $rowNumber, 'NIK: ' . $nik_penandatangan);

// Style untuk tanda tangan
$sheet->getStyle('F' . ($rowNumber - 4) . ':F' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Mengatur lebar kolom otomatis
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Laporan_Pengeluaran.xlsx"');
$writer->save('php://output');
exit;
?>
