<?php
include 'config.php';

$period = $_GET['period'] ?? 'month'; // Default ke bulanan

// Query untuk mengambil data pengeluaran berdasarkan periode
if ($period == 'day') {
    $query = "SELECT DATE(tanggal) AS label, SUM(realisasi) AS total FROM pengeluaran GROUP BY DATE(tanggal)";
} elseif ($period == 'month') {
    $query = "SELECT DATE_FORMAT(tanggal, '%M %Y') AS label, SUM(realisasi) AS total FROM pengeluaran GROUP BY YEAR(tanggal), MONTH(tanggal)";
} else { // Tahun
    $query = "SELECT YEAR(tanggal) AS label, SUM(realisasi) AS total FROM pengeluaran GROUP BY YEAR(tanggal)";
}

$result = $conn->query($query);

$labels = [];
$expenses = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['label'];
    $expenses[] = (float)$row['total'];
}

// Mengembalikan data dalam format JSON
echo json_encode(['labels' => $labels, 'expenses' => $expenses]);
?>
