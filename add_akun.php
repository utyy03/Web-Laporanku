<?php
include('config.php');

if (isset($_POST['kode']) && isset($_POST['deskripsi'])) {
    $kode = $_POST['kode'];
    $deskripsi = $_POST['deskripsi'];

    // Siapkan statement untuk menambah akun baru
    $stmt = $conn->prepare("INSERT INTO akun (kode, deskripsi) VALUES (?, ?)");
    
    // Periksa apakah statement berhasil disiapkan
    if (!$stmt) {
        echo 'error_prepare: ' . $conn->error;
        exit();
    }
    
    // Bind parameter
    $stmt->bind_param("ss", $kode, $deskripsi);

    // Eksekusi statement
    if ($stmt->execute()) {
        echo 'success'; // Jika berhasil, kirim respons success
    } else {
        echo 'error_execute: ' . $stmt->error; // Jika gagal, tampilkan pesan error
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
} else {
    echo 'missing_parameters'; // Jika parameter tidak lengkap
}
?>
