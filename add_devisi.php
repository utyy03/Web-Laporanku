<?php
include('config.php');

if (isset($_POST['kode']) && isset($_POST['nama'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];

    // Insert data into the 'devisi' table
    $stmt = $conn->prepare("INSERT INTO devisi (kode, nama) VALUES (?, ?)");
    $stmt->bind_param("ss", $kode, $nama);

    if ($stmt->execute()) {
        echo 'success'; // Send success response to AJAX
    } else {
        echo 'error'; // Send error response if insertion fails
    }
} else {
    echo 'missing_parameters'; // If parameters are missing, send an error
}
?>
