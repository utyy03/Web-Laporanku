<?php
session_start();
include 'config.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Default sorting
$order_by = "tanggal";
$order_dir = "ASC";

if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case "tanggal_asc":
            $order_by = "tanggal";
            $order_dir = "ASC";
            break;
        case "tanggal_desc":
            $order_by = "tanggal";
            $order_dir = "DESC";
            break;
        case "kode_asc":
            $order_by = "akun";
            $order_dir = "ASC";
            break;
        case "kode_desc":
            $order_by = "akun";
            $order_dir = "DESC";
            break;
    }
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// Query untuk data pengeluaran
$query = "SELECT id, akun, deskripsi, tanggal, deskripsi_kegiatan, realisasi, devisi 
          FROM pengeluaran 
          WHERE akun LIKE '%$keyword%' OR deskripsi LIKE '%$keyword%' OR deskripsi_kegiatan LIKE '%$keyword%' OR devisi LIKE '%$keyword%'
          ORDER BY $order_by $order_dir";
$result = $conn->query($query);

// Query untuk dropdown nama penandatangan dan jabatan
$signerResult = $conn->query("SELECT nama, nik FROM penandatangan");
$positionResult = $conn->query("SELECT jabatan FROM jabatan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<body class="dashboard-background">
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Header dengan gambar dan teks -->
        <div class="header-section">
            <div class="header-image">
                <img src="img/report-iconn.png" alt="Report Illustration">
            </div>
            <h2>AYO PILIH YANG AKAN MENJADI LAPORAN KAMU</h2>
        </div>

        <!-- Container utama untuk konten -->
        <div class="expense-container">
            <div id="notification" style="display: none; padding: 10px; background-color: #4CAF50; color: white; text-align: center; margin: 10px 0; border-radius: 5px;"></div>
            <div class="expense-actions">
                <div class="search-box">
                    <input type="text" placeholder="Search" id="search">
                    <select id="filter" name="filter">
                        <option value="">Sort By</option>
                        <option value="tanggal_asc">Tanggal Terlama</option>
                        <option value="tanggal_desc">Tanggal Terbaru</option>
                        <option value="kode_asc">Kode Akun Ascending</option>
                        <option value="kode_desc">Kode Akun Descending</option>
                    </select>
                    <button type="button" class="small-btn" id="generateReportBtn">Generate Report</button>
                </div>
            </div>

            <!-- Form Judul Dokumen dan Penandatangan -->
            <form id="expensesForm" method="POST" action="generate_report.php">
                <input type="hidden" name="report_title" value="Laporan Pengeluaran Kantor">
                
                <div class="input-pair">
                    <label for="judul_dokumen">Judul Dokumen</label>
                    <input type="text" id="judul_dokumen" name="judul_dokumen" value="Laporan LPT" readonly required>
                    <button type="button" id="editTitleButton" onclick="toggleEdit()" class="edit-title-btn">Edit</button>
                </div>




                <div class="input-pair">
                    <label for="sign_name">Nama Penandatangan:</label>
                    <select id="sign_name" name="sign_name" onchange="fillNIK()">
                        <?php while ($signerRow = $signerResult->fetch_assoc()): ?>
                            <option value="<?= $signerRow['nama'] ?>" data-nik="<?= $signerRow['nik'] ?>"><?= $signerRow['nama'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" onclick="openModal('addSignerModal')" class="add-btn">+</button>
                </div>

                <div class="input-pair">
                    <label for="sign_position">Jabatan Penandatangan:</label>
                    <select id="sign_position" name="sign_position">
                        <?php while ($positionRow = $positionResult->fetch_assoc()): ?>
                            <option value="<?= $positionRow['jabatan'] ?>"><?= $positionRow['jabatan'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" onclick="openModal('addPositionModal')" class="add-btn">+</button>
                </div>

                <div class="input-pair">
                    <label for="sign_nik">NIK Penandatangan:</label>
                    <input type="text" id="sign_nik" name="sign_nik" required readonly>
                </div>  

                <div class="expense-actions">
                    <button type="button" class="small-btn delete-selected-btn">Delete All</button>
                </div>


                <!-- Tabel pengeluaran -->
                <table class="expense-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>No</th>
                            <th>Akun</th>
                            <th>Deskripsi</th>
                            <th>Tanggal</th>
                            <th>Deskripsi Kegiatan</th>
                            <th>Realisasi (Rp)</th>
                            <th>Devisi</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="expensesTableBody">
    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <!-- Add the `selectCheckbox` class to each individual checkbox here -->
            <td><input type="checkbox" class="selectCheckbox" name="selected_ids[]" value="<?= $row['id'] ?>"></td>
            <td><?= $no++ ?></td>
            <td><?= $row['akun'] ?></td>
            <td><?= $row['deskripsi'] ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td><?= $row['deskripsi_kegiatan'] ?></td>
            <td>Rp <?= number_format($row['realisasi'], 0, ',', '.') ?></td>
            <td><?= $row['devisi'] ?></td>
            <td class="action-icons">
                <a href="javascript:void(0)" onclick="openSlideForm(<?= $row['id'] ?>)" class="edit-expense-btn">
                    <i class="fas fa-edit"></i>
                </a>
                <i class="fas fa-trash delete-btn" data-id="<?= $row['id'] ?>"></i>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

                </table>
            </form>
        </div>
    </div>

    <!-- Modal untuk Menambahkan Penandatangan -->
    <div id="addSignerModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addSignerModal')">&times;</span>
            <h2>Tambah Penandatangan</h2>
            <form id="addSignerForm">
                <div class="input-pair">
                    <label for="newSignerName">Nama:</label>
                    <input type="text" id="newSignerName" required>
                </div>
                <div class="input-pair">
                    <label for="newSignerNIK">NIK:</label>
                    <input type="text" id="newSignerNIK" required>
                </div>
                <button type="submit" class="submit-btn">Tambah</button>
            </form>
        </div>
    </div>


    <!-- Modal untuk Menambahkan Jabatan -->
    <div id="addPositionModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addPositionModal')">&times;</span>
            <h2>Tambah Jabatan</h2>
            <form id="addPositionForm">
                <div class="input-pair">
                    <label for="newPosition">Jabatan:</label>
                    <input type="text" id="newPosition" required>
                </div>
                <button type="submit" class="submit-btn">Tambah</button>
            </form>
        </div>
    </div>

<!-- Overlay Background -->
<div class="overlay" id="overlay" onclick="closeSlideForm()"></div>

<!-- Slide-In Form -->
<div class="slide-in-form" id="slideInForm">
    <span class="close-btn" onclick="closeSlideForm()">&times;</span>
    <h2>Edit Data Pengeluaran</h2>
    <form id="editForm">
        <input type="hidden" id="edit-id" name="id"> <!-- Input hidden untuk ID -->
        
        <div class="form-group">
            <label for="edit-akun">Akun:</label>
            <input type="text" id="edit-akun" name="akun" required>
        </div>
        
        <div class="form-group">
            <label for="edit-deskripsi">Deskripsi:</label>
            <input type="text" id="edit-deskripsi" name="deskripsi" required>
        </div>
        
        <div class="form-group">
            <label for="edit-tanggal">Tanggal:</label>
            <input type="date" id="edit-tanggal" name="tanggal" required>
        </div>
        
        <div class="form-group">
            <label for="edit-deskripsi_kegiatan">Deskripsi Kegiatan:</label>
            <input type="text" id="edit-deskripsi_kegiatan" name="deskripsi_kegiatan" required>
        </div>
        
        <div class="form-group">
            <label for="edit-realisasi">Realisasi (Rp):</label>
            <input type="number" id="edit-realisasi" name="realisasi" required>
        </div>
        
        <div class="form-group">
            <label for="edit-devisi">Devisi:</label>
            <input type="text" id="edit-devisi" name="devisi" required>
        </div>
        
        <button type="submit" class="submit-btn">Save</button>
    </form>
</div>

<script>
// Fungsi untuk membuka form slide-in dan mengisi data berdasarkan ID
function openSlideForm(id) {
    document.getElementById('overlay').style.display = 'block'; // Menampilkan overlay
    document.getElementById('slideInForm').classList.add('show'); // Menampilkan form slide-in

    // Ambil data berdasarkan ID dan masukkan ke dalam form
    fetch(`get_expense.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Memasukkan data yang diterima ke dalam input form
            if (data) {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-akun').value = data.akun;
                document.getElementById('edit-deskripsi').value = data.deskripsi;
                document.getElementById('edit-tanggal').value = data.tanggal;
                document.getElementById('edit-deskripsi_kegiatan').value = data.deskripsi_kegiatan;
                document.getElementById('edit-realisasi').value = data.realisasi;
                document.getElementById('edit-devisi').value = data.devisi;
            } else {
                alert("Data tidak ditemukan.");
                closeSlideForm(); // Tutup form jika data tidak ditemukan
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            alert("Terjadi kesalahan saat mengambil data.");
            closeSlideForm(); // Tutup form jika terjadi kesalahan
        });
}

// Fungsi untuk menutup form slide-in
function closeSlideForm() {
    document.getElementById('overlay').style.display = 'none'; // Sembunyikan overlay
    document.getElementById('slideInForm').classList.remove('show'); // Sembunyikan form slide-in
}

// Event listener untuk tombol submit pada form
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    // Mengirim data menggunakan fetch untuk update data
    fetch('update_expense.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === 'success') {
            showNotification('Data berhasil diperbarui', 'success');
            closeSlideForm(); // Tutup form setelah update
            location.reload(); // Refresh halaman untuk melihat perubahan
        } else {
            showNotification('Terjadi kesalahan saat memperbarui data', 'error');
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showNotification("Terjadi kesalahan saat menyimpan perubahan.", "error");
    });
});


</script>

    <script>
        $(document).ready(function() {
    // When the 'select all' checkbox is clicked
    $('#selectAllCheckbox').on('click', function() {
        // Set all checkboxes to checked or unchecked based on 'select all' checkbox state
        $('.selectCheckbox').prop('checked', this.checked);
    });

    // If any checkbox is unchecked, uncheck the 'select all' checkbox
    $('.selectCheckbox').on('click', function() {
        if (!$(this).prop('checked')) {
            $('#selectAllCheckbox').prop('checked', false);
        }
        // If all checkboxes are checked, check the 'select all' checkbox
        if ($('.selectCheckbox:checked').length === $('.selectCheckbox').length) {
            $('#selectAllCheckbox').prop('checked', true);
        }
    });
});

        $(document).ready(function() {
            // Filter dan Search
            $('#filter').change(function() {
                loadTableData($(this).val(), $('#search').val());
            });

            $('#search').on('input', function() {
                loadTableData($('#filter').val(), $(this).val());
            });

            function loadTableData(sortValue, keyword) {
                $.ajax({
                    url: 'view_expenses.php',
                    type: 'GET',
                    data: { sort: sortValue, keyword: keyword },
                    success: function(response) {
                        $('#expensesTableBody').html($(response).find('#expensesTableBody').html());
                    }
                });
            }

            // Event listener untuk tombol Edit
$(document).on('click', '.edit-btn', function() {
    const id = $(this).closest('tr').find('input[type="checkbox"]').val(); // Mendapatkan ID dari elemen checkbox di baris yang sama
    openSlideForm(id); // Memanggil fungsi untuk membuka form slide-in dengan data yang sesuai
});

document.querySelector('.close-btn').addEventListener('click', closeSlideForm);

            // Save Edit
            $('#editForm').submit(function(e) {
                e.preventDefault();
                const formData = {
                    id: $('#edit-id').val(),
                    akun: $('#edit-akun').val(),
                    deskripsi: $('#edit-deskripsi').val(),
                    tanggal: $('#edit-tanggal').val(),
                    deskripsi_kegiatan: $('#edit-deskripsi_kegiatan').val(),
                    realisasi: $('#edit-realisasi').val(),
                    devisi: $('#edit-devisi').val()
                };
                $.ajax({
                    url: 'update_expense.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            alert('Data updated successfully');
                            location.reload();
                        } else {
                            alert('Failed to update data');
                        }
                    }
                });
            });

            // Delete Button
            $('.delete-btn').click(function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this entry?')) {
                    $.ajax({
                        url: 'delete_expense.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response === 'success') {
                                alert('Data deleted successfully');
                                location.reload();
                            } else {
                                alert('Failed to delete data');
                            }
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
    $('.delete-selected-btn').click(function() {
        const selectedIds = [];
        $('input[name="selected_ids[]"]:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert("Pilih data yang ingin dihapus.");
            return;
        }

        if (!confirm("Apakah Anda yakin ingin menghapus data yang dipilih?")) {
            return;
        }

        $.ajax({
            url: 'delete_multiple_expenses.php',
            type: 'POST',
            data: { ids: selectedIds },
            success: function(response) {
                const data = typeof response === "string" ? JSON.parse(response) : response;
                if (data.status === 'success') {
                    alert(data.message); // Show success message
                    location.reload();
                } else {
                    alert(data.message); // Show specific error message
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", status, error);
                alert("Terjadi kesalahan saat menghubungi server.");
            }
        });
    });
});


document.getElementById("generateReportBtn").addEventListener("click", function(event) {
    event.preventDefault(); // Prevent default form submission
    
    // Disable the button to prevent multiple clicks
    const generateReportBtn = document.getElementById("generateReportBtn");
    generateReportBtn.disabled = true;

    // Prepare data for the check
    const title = document.getElementById("judul_dokumen").value;
    const dateToday = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format

    // First AJAX request to check for duplicate report
    fetch("check_duplicate_report.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ title: title, date: dateToday })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "exists") {
            // If duplicate exists, show error message
            showNotification("Laporan dengan judul dan tanggal yang sama sudah ada.", "error");
            generateReportBtn.disabled = false; // Re-enable the button
        } else {
            // If no duplicate, proceed with report generation
            let formData = new FormData(document.getElementById("expensesForm"));
            fetch("generate_report.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    showNotification(data.message, "success");
                } else if (data.status === "error") {
                    showNotification(data.message, "error");
                }
                generateReportBtn.disabled = false; // Re-enable button after response
            })
            .catch(error => {
                console.error("Error:", error);
                showNotification("Terjadi kesalahan. Silakan coba lagi.", "error");
                generateReportBtn.disabled = false; // Re-enable button in case of error
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showNotification("Terjadi kesalahan. Silakan coba lagi.", "error");
        generateReportBtn.disabled = false; // Re-enable button in case of error
    });
});



function showNotification(message, type) {
    const notification = document.getElementById("notification");
    notification.textContent = message;
    notification.style.backgroundColor = type === "success" ? "#4CAF50" : "#f44336"; // Hijau untuk sukses, merah untuk error
    notification.style.display = "block";

    // Sembunyikan notifikasi setelah 3 detik
    setTimeout(() => {
        notification.style.display = "none";
    }, 3000);
}

        function closeModal(modalId) {
            $('#' + modalId).hide();
        }

        function openSlideForm(id) {
    // Tampilkan overlay dan form slide
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('slideInForm').classList.add('show');

    // Fetch data dari server berdasarkan ID
    fetch(`get_expense.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Cek apakah data diterima dan masukkan ke form
            if (data) {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-akun').value = data.akun;
                document.getElementById('edit-deskripsi').value = data.deskripsi;
                document.getElementById('edit-tanggal').value = data.tanggal;
                document.getElementById('edit-deskripsi_kegiatan').value = data.deskripsi_kegiatan;
                document.getElementById('edit-realisasi').value = data.realisasi;
                document.getElementById('edit-devisi').value = data.devisi;
            } else {
                alert("Data tidak ditemukan.");
                closeSlideForm();
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            alert("Terjadi kesalahan saat mengambil data.");
            closeSlideForm();
        });
}
       
</script>

    <!-- JavaScript -->
    <script>
// Event listener untuk tombol Edit Pengeluaran
$(document).on('click', '.edit-expense-btn', function() {
    const id = $(this).closest('tr').find('input[type="checkbox"]').val();
    openSlideForm(id); // Memanggil fungsi untuk membuka form slide-in dengan data yang sesuai
});

// Fungsi Toggle Edit untuk Judul Dokumen
function toggleEdit() {
    const titleField = document.getElementById("judul_dokumen");
    const editButton = document.getElementById("editTitleButton");

    titleField.readOnly = !titleField.readOnly;
    if (!titleField.readOnly) {
        editButton.textContent = "Save";
    } else {
        editButton.textContent = "Edit";
        showNotification("Judul dokumen berhasil disimpan.", "success");
    }
}

        // Fungsi untuk mengisi NIK berdasarkan nama penandatangan yang dipilih
        function fillNIK() {
            const selectedOption = document.getElementById("sign_name").selectedOptions[0];
            document.getElementById("sign_nik").value = selectedOption.getAttribute("data-nik");
        }

        // Fungsi untuk membuka modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        // Fungsi untuk menutup modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Menangani submit form penandatangan baru
        document.getElementById("addSignerForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const name = document.getElementById("newSignerName").value;
            const nik = document.getElementById("newSignerNIK").value;
            $.post("add_signer.php", { name, nik }, function(response) {
                if (response === "success") {
                    // Menambahkan nama dan NIK ke dropdown Nama Penandatangan
                    const newOption = new Option(name, name);
                    newOption.setAttribute("data-nik", nik); // Menetapkan data-nik
                    document.getElementById("sign_name").add(newOption);

                    // Reset form dan tutup modal
                    document.getElementById("newSignerName").value = "";
                    document.getElementById("newSignerNIK").value = "";
                    closeModal("addSignerModal");
                } else {
                    alert("Gagal menambahkan penandatangan");
                }
            });
        });


        // Menangani submit form jabatan baru
        document.getElementById("addPositionForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const position = document.getElementById("newPosition").value;
            $.post("add_position.php", { position }, function(response) {
                if (response === "success") {
                    $("#sign_position").append(new Option(position, position));
                    closeModal("addPositionModal");
                } else {
                    alert("Gagal menambahkan jabatan");
                }
            });
        });


// Fungsi notifikasi tunggal
function showNotification(message, type) {
    const notification = document.getElementById("notification");
    notification.textContent = message;
    notification.style.backgroundColor = type === "success" ? "#4CAF50" : "#f44336";
    notification.style.display = "block";

    setTimeout(() => {
        notification.style.display = "none";
    }, 3000);
}
    </script>
</body>
</html>