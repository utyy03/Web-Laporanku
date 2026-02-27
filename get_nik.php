<?php
include 'config.php';
$name = $_GET['name'];
$query = "SELECT nik FROM penandatangan WHERE nama = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["nik" => $row['nik']]);
?>
