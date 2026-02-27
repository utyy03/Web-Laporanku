<?php
include('config.php');

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $akun = $_POST['akun'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];
    $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'];
    $realisasi = floatval($_POST['realisasi']);
    $devisi = $_POST['devisi'];

    $query = "UPDATE pengeluaran SET akun = ?, deskripsi = ?, tanggal = ?, deskripsi_kegiatan = ?, realisasi = ?, devisi = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssdis", $akun, $deskripsi, $tanggal, $deskripsi_kegiatan, $realisasi, $devisi, $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error_no_id";
}
?>
