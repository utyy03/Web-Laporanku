<?php
include 'config.php';

$period = $_GET['period'] ?? 'month'; // Default ke bulanan

// Array untuk menyimpan data pengeluaran berdasarkan divisi
$datasets = [];
$labels = [];

// Ambil semua divisi yang ada
$divisionsQuery = "SELECT DISTINCT devisi FROM pengeluaran";
$divisionsResult = $conn->query($divisionsQuery);

while ($divisionRow = $divisionsResult->fetch_assoc()) {
    $division = $divisionRow['devisi'];

    // Query untuk mendapatkan data pengeluaran per divisi sesuai periode
    if ($period == 'day') {
        $query = "SELECT DATE(tanggal) AS label, SUM(realisasi) AS total 
                  FROM pengeluaran 
                  WHERE devisi = ? 
                  GROUP BY DATE(tanggal)";
    } elseif ($period == 'week') {
        $query = "SELECT YEARWEEK(tanggal, 1) AS label, SUM(realisasi) AS total 
                  FROM pengeluaran 
                  WHERE devisi = ? 
                  GROUP BY YEARWEEK(tanggal, 1)";
    } elseif ($period == 'month') {
        $query = "SELECT DATE_FORMAT(tanggal, '%M %Y') AS label, SUM(realisasi) AS total 
                  FROM pengeluaran 
                  WHERE devisi = ? 
                  GROUP BY YEAR(tanggal), MONTH(tanggal)";
    } else { // Tahun
        $query = "SELECT YEAR(tanggal) AS label, SUM(realisasi) AS total 
                  FROM pengeluaran 
                  WHERE devisi = ? 
                  GROUP BY YEAR(tanggal)";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $division);
    $stmt->execute();
    $result = $stmt->get_result();

    $divisionData = [];
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['label'], $labels)) {
            $labels[] = $row['label'];
        }
        $divisionData[] = (float)$row['total'];
    }

    $datasets[] = [
        'label' => $division,
        'data' => $divisionData,
        'backgroundColor' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)) // Warna acak untuk tiap divisi
    ];
}

// Mengembalikan data dalam format JSON
echo json_encode(['labels' => $labels, 'datasets' => $datasets]);
?>
