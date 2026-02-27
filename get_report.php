<?php
include 'config.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Siapkan pernyataan untuk mengambil data berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM laporan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($data = $result->fetch_assoc()) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data not found']);
    }
    
    // Tutup pernyataan setelah digunakan
    $stmt->close();
} else {
    echo json_encode(['error' => 'No ID provided']);
}

// Menutup koneksi database
$conn->close();
?>
