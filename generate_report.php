<?php
session_start();
include('config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_ids'])) {
    // Retrieve data from form
    $selected_ids = $_POST['selected_ids'];
    $report_title = $_POST['report_title'];
    $nama_penandatangan = $_POST['sign_name'] ?? "Nama Penandatangan";
    $jabatan_penandatangan = $_POST['sign_position'] ?? "Jabatan Penandatangan";
    $nik_penandatangan = $_POST['sign_nik'] ?? "NIK Penandatangan";
    $tanggal_laporan = date("Y-m-d");

    // Store data in session for use in export_pdf.php or export_excel.php
    $_SESSION['selected_ids'] = $selected_ids;
    $_SESSION['report_title'] = $report_title;
    $_SESSION['sign_name'] = $nama_penandatangan;
    $_SESSION['sign_position'] = $jabatan_penandatangan;
    $_SESSION['sign_nik'] = $nik_penandatangan;

    // Get report title from form
    $judul_dokumen = $_POST['judul_dokumen'] ?? 'Laporan Tanpa Judul';

    // Check if a report with the same title and date already exists
    $checkQuery = "SELECT id FROM laporan WHERE nama_laporan = ? AND tanggal_dibuat = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $judul_dokumen, $tanggal_laporan);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If a report with the same title and date already exists, respond with an error
        echo json_encode(["status" => "error", "message" => "Laporan dengan judul dan tanggal yang sama sudah ada."]);
        $stmt->close();
        exit;
    }

    // If no duplicate exists, insert the new report
    $stmt->close();
    $insertQuery = "INSERT INTO laporan (nama_laporan, tanggal_dibuat) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ss", $judul_dokumen, $tanggal_laporan);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data laporan berhasil disimpan. Silakan unduh di menu Report."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan judul laporan ke database."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Tidak ada data yang dipilih."]);
}
exit;
