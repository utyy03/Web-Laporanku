<?php
include 'config.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare statement to search report name
$stmt = $conn->prepare("SELECT * FROM laporan WHERE nama_laporan LIKE ?");
$searchTerm = '%' . $query . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
$no = 1; // Row number
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
                        <td><input type='checkbox' class='reportCheckbox' value='{$row['id']}'></td>
                        <td>{$no}</td>
                        <td>" . htmlspecialchars($row['nama_laporan']) . "</td>
                        <td>" . htmlspecialchars(date("d/m/Y", strtotime($row['tanggal_dibuat']))) . "</td>
                        <td>
                            <a href='export_pdf.php?id=" . htmlspecialchars($row['id']) . "' class='report-icon'>
                                <img src='img/pdf-icon.png' alt='PDF'>
                            </a>
                            <a href='export_excel.php?id=" . htmlspecialchars($row['id']) . "' class='report-icon'>
                                <img src='img/excel-icon.png' alt='Excel'>
                            </a>
                        </td>
                        <td>
                            <div class='action-icons'>
                                <button class='edit-btn' data-id='" . htmlspecialchars($row['id']) . "' style='border: none; background: none; cursor: pointer;'>
                                    <i class='fas fa-edit' style='font-size: 18px; color: black;'></i>
                                </button>
                                <button class='delete-btn' data-id='" . htmlspecialchars($row['id']) . "' style='border: none; background: none; cursor: pointer;'>
                                    <i class='fas fa-trash' style='font-size: 18px; color: black;'></i>
                                </button>
                            </div>
                        </td>
                    </tr>";
        $no++;
    }
} else {
    // Message if no results found
    $output = "<tr><td colspan='6' style='text-align: center;'>Tidak ada laporan ditemukan</td></tr>";
}

echo $output;

// Close statement and connection
$stmt->close();
$conn->close();
?>
