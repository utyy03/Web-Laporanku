<?php
include 'config.php';

// Pastikan bahwa ID ada dalam permintaan POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Siapkan pernyataan untuk menghapus laporan berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM laporan WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    
    // Tutup pernyataan setelah digunakan
    $stmt->close();
} else {
    echo 'error'; // Berikan respons error jika tidak ada ID
}

// Menutup koneksi database
$conn->close();
?>
