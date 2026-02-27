<div class="sidebar">
    <!-- Sidebar header untuk logo dan ikon garis tiga -->
    <div class="sidebar-header">
        <button class="toggle-btn"><i class="fas fa-bars"></i></button>
        <img src="img/logo_laporanku.png" alt="Logo Laporanku" class="sidebar-logo">
    </div>

    <!-- Menu Sidebar -->
    <ul>
    <li><a href="beranda.php"><i class="fas fa-th-large icon"></i><span class="text">Dashboard</span></a></li>
    <li><a href="input_pengeluaran.php"><i class="fas fa-plus-circle icon"></i><span class="text">Add</span></a></li>
    <li><a href="view_expenses.php"><i class="fas fa-history icon"></i><span class="text">View Expenses</span></a></li>
    <li><a href="report.php"><i class="fas fa-file-alt icon"></i><span class="text">Report</span></a></li>
    <li><a href="analysis.php"><i class="fas fa-chart-bar icon"></i><span class="text">Analysis</span></a></li> <!-- Menu Analysis -->
    <li><a href="logout.php"><i class="fas fa-sign-out-alt icon"></i><span class="text">Logout</span></a></li>
</ul>

</div>

<!-- JavaScript untuk Toggle Sidebar -->
<script>
    const toggleBtn = document.querySelector('.toggle-btn');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.main-content');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    });
</script>
