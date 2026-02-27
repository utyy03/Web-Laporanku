<?php
include 'config.php';

$title = $_GET['title'];
$query = "SELECT COUNT(*) as count FROM laporan WHERE nama_laporan LIKE ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    "exists" => $row['count'] > 0,
    "count" => $row['count']
]);
?>
