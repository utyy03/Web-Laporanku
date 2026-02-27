<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_utama = $_POST['judul_utama'];
    $sub_judul = $_POST['sub_judul'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $akun = $_POST['akun'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];
    $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'];
    $realisasi = $_POST['realisasi'];
    $devisi = $_POST['devisi'];

    // Masukkan data ke database
    $query = "INSERT INTO pengeluaran (judul_utama, sub_judul, bulan, tahun, akun, deskripsi, tanggal, deskripsi_kegiatan, realisasi, devisi) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssds", $judul_utama, $sub_judul, $bulan, $tahun, $akun, $deskripsi, $tanggal, $deskripsi_kegiatan, $realisasi, $devisi);
    $stmt->execute();

    echo json_encode(["success" => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="dashboard-background add-page">
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="header-section">
            <img src="img/person_asking.png" alt="Ilustrasi orang bertanya" class="illustration">
            <h2>ADA PENGELUARAN APA HARI INI?</h2>
        </div>

        <!-- Notifikasi sukses -->
        <div id="success-message" style="display:none; color: green; font-weight: bold; margin-bottom: 20px;">
            Data berhasil ditambahkan!
        </div>

        <form id="pengeluaranForm" method="POST" action="input_pengeluaran.php">
            <div class="section">
                <h3>1. JUDUL</h3>
                <div class="input-pair">
                    <label for="judul_utama">Judul Utama</label>
                    <input type="text" id="judul_utama" name="judul_utama" value="LEMBAR PERTANGGUNGJAWABAN TRANSAKSI (LPT)" readonly>
                    <button type="button" onclick="toggleEdit('judul_utama', this)" class="edit-btn">Edit</button>
                </div>
                <div class="input-pair">
                    <label for="sub_judul">Sub Judul</label>
                    <input type="text" id="sub_judul" name="sub_judul" value="KANTOR CABANG BOGOR" readonly>
                    <button type="button" onclick="toggleEdit('sub_judul', this)" class="edit-btn">Edit</button>
                </div>
                <div class="input-pair">
                    <label for="bulan">Bulan</label>
                    <select id="bulan" name="bulan" required>
                        <option value="" disabled selected>Pilih Bulan</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>
                <div class="input-pair">
                    <label for="tahun">Tahun</label>
                    <select id="tahun" name="tahun" required>
                        <option value="" disabled selected>Pilih Tahun</option>
                        <?php
                        for ($year = date("Y"); $year >= 2000; $year--) {
                            echo "<option value='$year'>$year</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="section">
                <h3>2. ISI</h3>
                <div class="input-pair">
                    <label for="akun">Akun</label>
                    <select id="akun" name="akun" required>
                        <option value="" disabled selected>Pilih Kode Akun</option>
                        <?php
                        $result = $conn->query("SELECT kode FROM akun");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='".$row['kode']."'>".$row['kode']."</option>";
                        }
                        ?>
                    </select>
                    <button type="button" onclick="openModal('addAkunModal')" class="add-btn">+</button>
                </div>
                <div class="input-pair">
                    <label for="deskripsi">Deskripsi</label>
                    <input type="text" id="deskripsi" name="deskripsi" readonly required>
                </div>
                <div class="input-pair">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
                <div class="input-pair">
                    <label for="deskripsi_kegiatan">Deskripsi Kegiatan</label>
                    <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" placeholder="Deskripsi kegiatan..." required></textarea>
                </div>
                <div class="input-pair">
                    <label for="realisasi">Realisasi (Rp)</label>
                    <input type="number" id="realisasi" name="realisasi" placeholder="Nominal" required>
                </div>
                <div class="input-pair">
                    <label for="devisi">Devisi</label>
                    <select id="devisi" name="devisi">
                        <option value="" selected>Tidak ada devisi</option>
                        <option value="P001">P001 - Pelayanan</option>
                        <option value="K001">K001 - Keuangan</option>
                        <option value="S001">S001 - SDM</option>
                        <?php
                        $result = $conn->query("SELECT kode, nama FROM devisi");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['kode'] . "'>" . $row['kode'] . " - " . $row['nama'] . "</option>";
                        }
                        ?>
                    </select>
                    <button type="button" onclick="openModal('addDevisiModal')" class="add-btn">+</button>
                </div>
            </div>

            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>

    <script>
        function toggleEdit(fieldId, button) {
            const field = document.getElementById(fieldId);
            field.readOnly = !field.readOnly;
            button.textContent = field.readOnly ? "Edit" : "Save";
        }

        $(document).ready(function() {
            $('#pengeluaranForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: 'input_pengeluaran.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            $('#success-message').show();
                            $('#pengeluaranForm')[0].reset();
                            setTimeout(function() {
                                $('#success-message').fadeOut();
                            }, 3000);
                        }
                    }
                });
            });

            $('#akun').change(function() {
                var kodeAkun = $(this).val();
                $.ajax({
                    url: 'get_deskripsi.php',
                    type: 'POST',
                    data: { kode: kodeAkun },
                    success: function(response) {
                        $('#deskripsi').val(response);
                    }
                });
            });

            $('#addAkunForm').submit(function(e) {
                e.preventDefault();
                const kode = $('#newAkunKode').val();
                const deskripsi = $('#newAkunDeskripsi').val();

                $.ajax({
                    url: 'add_akun.php',
                    type: 'POST',
                    data: { kode: kode, deskripsi: deskripsi },
                    success: function(response) {
                        if (response === 'success') {
                            $('#akun').append(new Option(kode, kode));
                            $('#newAkunKode').val('');
                            $('#newAkunDeskripsi').val(''); 
                            closeModal('addAkunModal');
                            openSuccessModal();
                        } else {
                            alert("Gagal menambahkan kode akun: " + response);
                        }
                    }
                });
            });

            $('#addDevisiForm').submit(function(e) {
                e.preventDefault();
                const devisi = $('#newDevisi').val();
                const [kode, nama] = devisi.split(" - ");
                if (!kode || !nama) {
                    alert("Format salah! Gunakan format 'Kode - Nama', contoh: 'P001 - Pelayanan'.");
                    return;
                }
                $.ajax({
                    url: 'add_devisi.php',
                    type: 'POST',
                    data: { kode: kode.trim(), nama: nama.trim() },
                    success: function(response) {
                        if (response === 'success') {
                            $('#devisi').append(new Option(devisi, kode));
                            $('#newDevisi').val(''); // Kosongkan input devisi
                            closeModal('addDevisiModal');
                            openSuccessModal();
                        } else {
                            alert("Gagal menambahkan devisi: " + response);
                        }
                    }
                });
            });
        });

        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        function openSuccessModal() {
        document.getElementById("successModal").style.display = "block";
    }

        function closeSuccessModal() {
        document.getElementById("successModal").style.display = "none";
    }

    </script>

    <!-- Modal Tambah Akun -->
    <div id="addAkunModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addAkunModal')">&times;</span>
            <h2>Tambah Data</h2>
            <form id="addAkunForm">
                <div class="input-pair">
                    <label for="newAkunKode">Akun</label>
                    <input type="text" id="newAkunKode" name="kode" required>
                </div>
                <div class="input-pair">
                    <label for="newAkunDeskripsi">Deskripsi</label>
                    <input type="text" id="newAkunDeskripsi" name="deskripsi" required>
                </div>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Devisi -->
    <div id="addDevisiModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addDevisiModal')">&times;</span>
            <h2>Tambah Devisi</h2>
            <form id="addDevisiForm">
                <div class="input-pair">
                    <label for="newDevisi">Devisi</label>
                    <input type="text" id="newDevisi" name="devisi" placeholder="Contoh: P001 - Pelayanan" required>
                </div>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>
    
    <!-- Modal Sukses -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="success-icon">
                <img src="img/success-icon.png" alt="Success">
            </div>
            <h3>YEY!!! BERHASIL DITAMBAHKAN</h3>
            <button onclick="closeSuccessModal()" class="submit-btn">Close</button>
        </div>
    </div>
    
</body>
</html>
