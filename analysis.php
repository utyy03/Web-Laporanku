<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Proses input, edit, atau hapus pemasukan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add' && isset($_POST['pemasukan'])) {
            // Tambah pemasukan baru
            $pemasukan = $_POST['pemasukan'];
            $query = "INSERT INTO pemasukan (jumlah, tanggal) VALUES (?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("d", $pemasukan);
            $stmt->execute();
        } elseif ($action === 'edit' && isset($_POST['jumlah'])) {
            // Edit pemasukan
            $new_amount = $_POST['jumlah'];
            $query = "UPDATE pemasukan SET jumlah = ? WHERE id = (SELECT MAX(id) FROM pemasukan)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("d", $new_amount);
            $stmt->execute();
        } elseif ($action === 'delete') {
            // Hapus pemasukan
            $query = "DELETE FROM pemasukan";
            $conn->query($query);
        }
    }
}

// Ambil total pemasukan dan pengeluaran dari database
$total_pemasukan = $conn->query("SELECT SUM(jumlah) AS total_pemasukan FROM pemasukan")->fetch_assoc()['total_pemasukan'] ?? 0;
$total_pengeluaran = $conn->query("SELECT SUM(realisasi) AS total_pengeluaran FROM pengeluaran")->fetch_assoc()['total_pengeluaran'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="dashboard-background">
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Header dengan Gambar dan Teks -->
        <div class="header-section">
            <img src="img/graphic_icon.png" alt="Graphic Icon" class="header-icon">
            <h2>Jangan Lupa Untuk Melihat Grafik Ya</h2>
        </div>

        <!-- Form Pemasukan -->
        <div class="input-section">
            <form method="POST" action="" class="form-pemasukan">
                <label for="pemasukan">Pemasukan:</label>
                <input type="number" name="pemasukan" placeholder="Masukkan jumlah pemasukan" required class="input-pemasukan">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn-pemasukan">Simpan Pemasukan</button>
            </form>
        </div>

        <!-- Kotak Pemasukan dan Pengeluaran -->
        <div class="box-section">
            <div class="box pemasukan-box" onclick="openModal()" style="cursor: pointer;">
                <h3>Pemasukan</h3>
                <p>Rp. <?= number_format($total_pemasukan, 0, ',', '.') ?></p>
            </div>
            <div class="box pengeluaran-box" style="cursor: not-allowed;">
                <h3>Pengeluaran</h3>
                <p>Rp. <?= number_format($total_pengeluaran, 0, ',', '.') ?></p>
            </div>
        </div>

        <!-- Grafik Pemasukan dan Pengeluaran -->
        <div class="chart-box">
            <div class="chart-section">
                <h3>Analysis Pemasukan Dan Pengeluaran</h3>
                <div class="chart-container">
                    <canvas id="incomeExpenseChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Pengeluaran Berdasarkan Periode -->
        <div class="chart-box">
            <div class="chart-section">
                <h3>Pengeluaran Berdasarkan Periode</h3>
                <label for="expense-filter">Pilih Periode:</label>
                <select id="expense-filter" name="expense-filter">
                    <option value="day">Harian</option>
                    <option value="week">Mingguan</option>
                    <option value="month" selected>Bulanan</option>
                    <option value="year">Tahunan</option>
                </select>
                <div class="chart-container">
                    <canvas id="monthlyExpenseChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Pengeluaran per Divisi -->
        <div class="chart-box">
            <div class="chart-section">
                <h3>Pengeluaran per Divisi</h3>
                <label for="division-period-filter">Pilih Periode:</label>
                <select id="division-period-filter" name="division-period-filter">
                    <option value="day">Harian</option>
                    <option value="week">Mingguan</option>
                    <option value="month" selected>Bulanan</option>
                    <option value="year">Tahunan</option>
                </select>
                <div class="chart-container">
                    <canvas id="divisionExpenseChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Modal untuk Detail Pemasukan -->
        <div id="modalForm" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Detail Pemasukan</h2>
                <form method="POST" action="">
                    <label for="jumlah">Total Pemasukan:</label>
                    <input type="number" name="jumlah" id="jumlah" value="<?= $total_pemasukan ?>" required>
                    <input type="hidden" name="action" value="edit">
                    <button type="submit" class="btn-save">Simpan</button>
                    <button type="button" onclick="deleteIncome()" class="btn-delete">Hapus</button>
                </form>
            </div>
        </div>

        <!-- Script untuk Chart.js dan Modal -->
        <script>
            function openModal() {
                document.getElementById("modalForm").style.display = "flex";
                document.getElementById("jumlah").value = <?= $total_pemasukan ?>;
            }

            function closeModal() {
                document.getElementById("modalForm").style.display = "none";
            }

            function deleteIncome() {
                if (confirm("Apakah Anda yakin ingin menghapus semua data pemasukan?")) {
                    document.getElementById("modalForm").querySelector("form").action = "";
                    document.getElementById("modalForm").querySelector("input[name='action']").value = "delete";
                    document.getElementById("modalForm").querySelector("form").submit();
                }
            }

            // Grafik Pemasukan dan Pengeluaran
            const incomeExpenseChart = new Chart(document.getElementById('incomeExpenseChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: [''],
                    datasets: [
                        {
                            label: 'Pemasukan (Rp)',
                            data: [<?= $total_pemasukan ?>],
                            backgroundColor: '#4CAF50'
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: [<?= $total_pengeluaran ?>],
                            backgroundColor: '#F44336'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { display: false },
                        y: {
                            beginAtZero: true,
                            ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString(); } }
                        }
                    }
                }
            });

            // Grafik Pengeluaran Berdasarkan Periode
            const monthlyExpenseChartCanvas = document.getElementById('monthlyExpenseChart').getContext('2d');
            let monthlyExpenseChart;

            function updateExpenseChart(labels, data) {
                if (monthlyExpenseChart) monthlyExpenseChart.destroy();
                monthlyExpenseChart = new Chart(monthlyExpenseChartCanvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{ label: 'Pengeluaran (Rp)', data: data, backgroundColor: '#2196F3' }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString(); } }
                            }
                        }
                    }
                });
            }

            function fetchExpenseDataByPeriod(period) {
                fetch('fetch_expense_by_period.php?period=' + period)
                    .then(response => response.json())
                    .then(data => { updateExpenseChart(data.labels, data.expenses); });
            }

            document.getElementById('expense-filter').addEventListener('change', function() {
                fetchExpenseDataByPeriod(this.value);
            });
            fetchExpenseDataByPeriod('month');

            // Grafik Pengeluaran per Divisi
            const divisionExpenseChartCanvas = document.getElementById('divisionExpenseChart').getContext('2d');
            let divisionExpenseChart;

            function updateDivisionChart(labels, datasets) {
                if (divisionExpenseChart) divisionExpenseChart.destroy();
                divisionExpenseChart = new Chart(divisionExpenseChartCanvas, {
                    type: 'bar',
                    data: { labels: labels, datasets: datasets },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString(); } }
                            }
                        }
                    }
                });
            }

            function fetchExpenseDataByDivisionPeriod(period) {
                fetch('fetch_expense_by_division_period.php?period=' + period)
                    .then(response => response.json())
                    .then(data => { updateDivisionChart(data.labels, data.datasets); });
            }

            document.getElementById('division-period-filter').addEventListener('change', function() {
                fetchExpenseDataByDivisionPeriod(this.value);
            });
            fetchExpenseDataByDivisionPeriod('month');
        </script>
    </div>
</body>
</html>
