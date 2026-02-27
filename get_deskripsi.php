<?php
include('config.php');

if (isset($_POST['kode'])) {
    $kode = $_POST['kode'];
    $query = "SELECT deskripsi FROM akun WHERE kode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo $row['deskripsi'];
}
?>
