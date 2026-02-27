<?php
include 'config.php';

// Pastikan bahwa ID dan nama laporan ada dalam permintaan POST
if (isset($_POST['id']) && isset($_POST['nama_laporan']) && !empty($_POST['nama_laporan'])) {
    $id = $_POST['id'];
    $nama_laporan = $_POST['nama_laporan'];

    // Siapkan pernyataan untuk mengupdate nama laporan berdasarkan ID
    $stmt = $conn->prepare("UPDATE laporan SET nama_laporan = ? WHERE id = ?");
    $stmt->bind_param("si", $nama_laporan, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    // Tutup pernyataan dan koneksi
    $stmt->close();
    $conn->close();
} else {
    echo 'error'; // Berikan respons error jika input tidak lengkap
}
?>
