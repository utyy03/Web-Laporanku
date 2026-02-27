<?php
session_start();
include 'config.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Periksa apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position = $_POST['position'];

    // Query untuk menambahkan jabatan ke database
    $stmt = $conn->prepare("INSERT INTO jabatan (jabatan) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $position);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
    } else {
        echo "error";
    }
}
$conn->close();
?>
