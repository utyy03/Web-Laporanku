<?php
session_start();
include('config.php');

header("Content-Type: application/json");

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(["status" => "error", "message" => "Not authorized"]);
    exit();
}

// Retrieve the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'] ?? '';
$date = $data['date'] ?? '';

// Check for existing report with the same title and date
$query = "SELECT id FROM laporan WHERE nama_laporan = ? AND tanggal_dibuat = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $title, $date);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Report already exists
    echo json_encode(["status" => "exists"]);
} else {
    // No report found
    echo json_encode(["status" => "not_exists"]);
}

$stmt->close();
$conn->close();
?>
