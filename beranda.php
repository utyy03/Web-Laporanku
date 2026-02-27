<?php
session_start();
include 'config.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="dashboard-background">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar header untuk logo dan ikon garis tiga -->
        <div class="sidebar-header">
            <button class="toggle-btn"><i class="fas fa-bars"></i></button>
            <img src="img/logo_laporanku.png" alt="Logo Laporanku" class="sidebar-logo">
        </div>
        <ul>
            <li><a href="beranda.php"><i class="fas fa-th-large icon"></i><span class="text">Dashboard</span></a></li>
            <li><a href="input_pengeluaran.php"><i class="fas fa-plus-circle icon"></i><span class="text">Add</span></a></li>
            <li><a href="view_expenses.php"><i class="fas fa-history icon"></i><span class="text">View Expenses</span></a></li>
            <li><a href="report.php"><i class="fas fa-file-alt icon"></i><span class="text">Report</span></a></li>
            <li><a href="analysis.php"><i class="fas fa-chart-bar icon"></i><span class="text">Analysis</span></a></li> <!-- Menu Analysis -->
            <li><a href="logout.php"><i class="fas fa-sign-out-alt icon"></i><span class="text">Logout</span></a></li>
        </ul>
    </div>

    <!-- Dashboard card container -->    
    <div class="content">
        <div class="dashboard-welcome">
            <h2>Selamat Datang di Laporanku</h2>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-header">
                <div class="welcome-section">
                    <h1 class="brand-title"><span class="highlight-lapo">Lapor</span><span class="highlight-anku">anKu</span></h1>
                    <p class="justify">Laporanku adalah Website yang dirancang untuk memudahkan pencatatan dan Pengelolaan Keuangan. Jadi ayo membuat Laporan dengan lebih mudah menggunakan Laporanku</p>
                    <button class="start-btn" id="scrollToFeatures">Start</button>
                </div>
                <div class="dashboard-image">
                    <img src="img/image-dashboard.png" alt="Dashboard Illustration">
                </div>
            </div>
        </div>

        <!-- Bagian Fitur -->
        <section id="features" class="features-section">
   <h2>Ayo Coba Semua Fitur yang ada di Laporanku!!</h2>
   <div class="features-grid">
      <div class="feature-box">
         <img src="img/add_icon.png" alt="Add Data">
         <h3>Add Data</h3>
         <p class="justify">Fitur ini digunakan untuk menambahkan data pengeluaran baru. Admin dapat memasukkan detail pengeluaran seperti kode, deskripsi, harga, dan informasi lainnya yang nantinya akan tercatat dalam sistem.</p>
         <a href="input_pengeluaran.php" class="start-btn">Start</a>
      </div>
      <div class="feature-box">
         <img src="img/view_icon.png" alt="View Expenses">
         <h3>View Expenses</h3>
         <p class="justify">Fitur ini digunakan untuk melihat semua pengeluaran yang telah dimasukkan melalui fitur Add Data. Setelah admin menambahkan data, pengeluaran tersebut akan ditampilkan di menu ini untuk dilihat dan dikelola lebih lanjut.</p>
         <a href="view_expenses.php" class="start-btn">Start</a>
      </div>
      <div class="feature-box">
         <img src="img/report_icon.png" alt="Report">
         <h3>Report</h3>
         <p class="justify">Fitur ini memungkinkan admin mengunduh laporan pengeluaran. Setelah memilih data di View Expenses, admin dapat mencetak atau mengunduh laporan yang diinginkan.</p>
         <a href="report.php" class="start-btn">Start</a>
      </div>
      <div class="feature-box">
         <img src="img/analysis_icon.png" alt="Contact Us">
         <h3>Analysis</h3>
         <p class="justify">Fitur ini akan menampilkan analisis pemasukan dan pengeluaran dalam bentuk grafik.</p>
         <a href="analysis.php" class="start-btn">Start</a>
        </div>
    </div>
</section>
        <!-- Footer Section -->
        <footer class="footer-section">
            <p>Ikutin Kami</p>
            <div class="social-icons">
                <a href="#"><img src="img/instagram.png" alt="Instagram"></a>
                <a href="#"><img src="img/twitter.png" alt="Twitter"></a>
                <a href="#"><img src="img/linkedin.png" alt="LinkedIn"></a>
                <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
                <a href="#"><img src="img/youtube.png" alt="YouTube"></a>
            </div>
            <p>© 2024 All Rights Reserved PT</p>
        </footer>
    </div>

    <!-- Sidebar toggle script -->
    <script>
        const toggleBtn = document.querySelector('.toggle-btn');
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });

        // Smooth scroll to features section
        document.getElementById('scrollToFeatures').addEventListener('click', function() {
            document.getElementById('features').scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>
