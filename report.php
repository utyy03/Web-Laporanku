<?php
// Start session and check if admin is logged in
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Get the selected month and year from GET parameters
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Prepare the query with filtering based on month and year
$query = "SELECT * FROM laporan WHERE 1";

// If month is selected, add to query
if (!empty($month)) {
    $query .= " AND MONTH(tanggal_dibuat) = ?";
}

// If year is selected, add to query
if (!empty($year)) {
    $query .= " AND YEAR(tanggal_dibuat) = ?";
}

$query .= " ORDER BY tanggal_dibuat DESC"; // Default sorting by date in descending order

$stmt = $conn->prepare($query);

// Bind parameters if month and year are provided
if (!empty($month) && !empty($year)) {
    $stmt->bind_param("ss", $month, $year);
} elseif (!empty($month)) {
    $stmt->bind_param("s", $month);
} elseif (!empty($year)) {
    $stmt->bind_param("s", $year);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="dashboard-background">
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="header-section">
            <img src="img/loading-icon.png" alt="Loading Icon" class="header-image">
            <h2>AYO DOWNLOAD LAPORANNYA</h2>
        </div>
         <!-- Dropdown for filter by month and year -->
         <div class="filter-container">
            <form method="get" action="report.php">
            <select name="month" id="month" class="filter-dropdown">
            <option value="">Pilih Bulan</option>
                    <option value="">Semua Bulan</option>
                    <option value="01" <?= ($month == "01") ? "selected" : ""; ?>>Januari</option>
                    <option value="02" <?= ($month == "02") ? "selected" : ""; ?>>Februari</option>
                    <option value="03" <?= ($month == "03") ? "selected" : ""; ?>>Maret</option>
                    <option value="04" <?= ($month == "04") ? "selected" : ""; ?>>April</option>
                    <option value="05" <?= ($month == "05") ? "selected" : ""; ?>>Mei</option>
                    <option value="06" <?= ($month == "06") ? "selected" : ""; ?>>Juni</option>
                    <option value="07" <?= ($month == "07") ? "selected" : ""; ?>>Juli</option>
                    <option value="08" <?= ($month == "08") ? "selected" : ""; ?>>Agustus</option>
                    <option value="09" <?= ($month == "09") ? "selected" : ""; ?>>September</option>
                    <option value="10" <?= ($month == "10") ? "selected" : ""; ?>>Oktober</option>
                    <option value="11" <?= ($month == "11") ? "selected" : ""; ?>>November</option>
                    <option value="12" <?= ($month == "12") ? "selected" : ""; ?>>Desember</option>
                </select>
                
                  </select>
                <select name="year" id="year" class="filter-dropdown">
                    <option value="">Pilih Tahun</option>
                    <?php
                        $currentYear = date("Y");
                        for ($i = $currentYear; $i >= 2000; $i--) {
                            echo "<option value='$i' " . (($year == $i) ? 'selected' : '') . ">$i</option>";
                        }
                    ?>
                </select>
                <button type="submit" class="filter-btn">Filter</button>
            </form>
        </div>

        <div class="report-container">
            <div class="search-container">
                <input type="text" id="reportSearch" placeholder="Search" class="search-box">
            </div>

            <button id="deleteSelectedReports" class="small-btn delete-selected-btn">Delete All</button>

            <!-- Main content container -->
            <div class="report-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllReports"></th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal Dibuat</th>
                            <th>Cetak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><input type='checkbox' class='reportCheckbox' value='{$row['id']}'></td>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_laporan']) . "</td>";
                            echo "<td>" . date("d/m/Y", strtotime($row['tanggal_dibuat'])) . "</td>";
                            echo "<td>
                                    <a href='export_pdf.php?id={$row['id']}' class='report-icon'>
                                        <img src='img/pdf-icon.png' alt='PDF'>
                                    </a>
                                    <a href='export_excel.php?id={$row['id']}' class='report-icon'>
                                        <img src='img/excel-icon.png' alt='Excel'>
                                    </a>
                                  </td>";
                            echo "<td>
                                    <div class='action-icons'>
                                        <button class='edit-btn' data-id='{$row['id']}' style='border: none; background: none; cursor: pointer;'>
                                            <i class='fas fa-edit' style='font-size: 18px; color: black;'></i>
                                        </button>
                                        <button class='delete-btn' data-id='{$row['id']}' style='border: none; background: none; cursor: pointer;'>
                                            <i class='fas fa-trash' style='font-size: 18px; color: black;'></i>
                                        </button>
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                let deleteInProgress = false; // Flag to prevent multiple requests

                // Select all checkbox
                $('#selectAllReports').on('click', function() {
                    $('.reportCheckbox').prop('checked', this.checked);
                });

                // Delete all selected reports with debouncing and disable button during processing
                $('#deleteSelectedReports').on('click', function() {
                    if (deleteInProgress) return; // Exit if delete is already in progress
                    deleteInProgress = true;

                    const selectedReports = $('.reportCheckbox:checked').map(function() {
                        return $(this).val();
                    }).get();

                    if (selectedReports.length === 0) {
                        alert("Pilih setidaknya satu laporan untuk dihapus.");
                        deleteInProgress = false;
                        return;
                    }

                    if (confirm('Apakah Anda yakin ingin menghapus laporan yang dipilih?')) {
                        $('#deleteSelectedReports').prop('disabled', true); // Disable button
                        $.ajax({
                            url: 'delete_multiple_reports.php',
                            type: 'POST',
                            data: { ids: selectedReports },
                            success: function(response) {
                                if (response === 'success') {
                                    alert('Laporan berhasil dihapus');
                                    location.reload();
                                } else {
                                    alert('Gagal menghapus laporan');
                                }
                                deleteInProgress = false;
                                $('#deleteSelectedReports').prop('disabled', false); // Enable button after response
                            },
                            error: function() {
                                alert('Terjadi kesalahan saat menghubungi server.');
                                deleteInProgress = false;
                                $('#deleteSelectedReports').prop('disabled', false); // Enable button after error
                            }
                        });
                    } else {
                        deleteInProgress = false; // Reset flag if deletion is canceled
                    }
                });

                // Handle the edit functionality
                $(document).on('click', '.edit-btn', function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: 'get_report.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            var data = JSON.parse(response);
                            $('#edit-id').val(data.id);
                            $('#edit-nama-laporan').val(data.nama_laporan);
                            $('#editModal').show();
                        }
                    });
                });

                // Edit form submission
                $('#editForm').submit(function(e) {
                    e.preventDefault();
                    var id = $('#edit-id').val();
                    var nama_laporan = $('#edit-nama-laporan').val();

                    $.ajax({
                        url: 'update_report.php',
                        type: 'POST',
                        data: { id: id, nama_laporan: nama_laporan },
                        success: function(response) {
                            if (response === 'success') {
                                alert('Data berhasil diperbarui');
                                location.reload();
                            } else {
                                alert('Gagal memperbarui data');
                            }
                        }
                    });
                });

                // Delete individual report
                $(document).on('click', '.delete-btn', function() {
                    if (confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
                        var id = $(this).data('id');
                        $.ajax({
                            url: 'delete_report.php',
                            type: 'POST',
                            data: { id: id },
                            success: function(response) {
                                if (response === 'success') {
                                    alert('Laporan berhasil dihapus');
                                    location.reload();
                                } else {
                                    alert('Gagal menghapus laporan');
                                }
                            }
                        });
                    }
                });

                // Search functionality
                $('#reportSearch').on('input', function() {
                    var searchValue = $(this).val();
                    $.ajax({
                        url: 'search_report.php', // Endpoint for handling search queries
                        type: 'GET',
                        data: { query: searchValue },
                        success: function(data) {
                            $('#reportTableBody').html(data);
                        },
                        error: function() {
                            alert("Gagal memuat hasil pencarian.");
                        }
                    });
                });
            });

            // Close modal function
            function closeModal(modalId) {
                $('#' + modalId).hide();
            }
        </script>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
                <h2>Edit Laporan</h2>
                <form id="editForm">
                    <input type="hidden" id="edit-id">
                    <div class="input-pair">
                        <label for="edit-nama-laporan">Nama Laporan</label>
                        <input type="text" id="edit-nama-laporan" required>
                    </div>
                    <button type="submit" class="submit-btn">Save</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
