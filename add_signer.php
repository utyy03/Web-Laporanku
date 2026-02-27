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
    $name = $_POST['name'];
    $nik = $_POST['nik'];

    // Query untuk menambahkan penandatangan ke database
    $stmt = $conn->prepare("INSERT INTO penandatangan (nama, nik) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $name, $nik);
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
