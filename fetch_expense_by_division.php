<?php
include 'config.php';

$division = $_GET['division'] ?? 'all';

// Query untuk mengambil data pengeluaran berdasarkan divisi
if ($division == 'all') {
    $query = "SELECT DATE_FORMAT(tanggal, '%M %Y') AS label, SUM(realisasi) AS total 
              FROM pengeluaran 
              GROUP BY YEAR(tanggal), MONTH(tanggal)";
} else {
    $query = "SELECT DATE_FORMAT(tanggal, '%M %Y') AS label, SUM(realisasi) AS total 
              FROM pengeluaran 
              WHERE devisi = ?
              GROUP BY YEAR(tanggal), MONTH(tanggal)";
}

$stmt = $conn->prepare($query);

if ($division != 'all') {
    $stmt->bind_param("s", $division);
}

$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$expenses = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['label'];
    $expenses[] = (float)$row['total'];
}

// Mengembalikan data dalam format JSON
echo json_encode(['labels' => $labels, 'expenses' => $expenses]);
?>
