<?php
include 'config.php';

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// Query pencarian berdasarkan kata kunci di kolom akun, deskripsi, deskripsi_kegiatan, atau devisi
$query = "SELECT id, akun, deskripsi, tanggal, deskripsi_kegiatan, realisasi, devisi 
          FROM pengeluaran 
          WHERE akun LIKE '%$keyword%' 
             OR deskripsi LIKE '%$keyword%' 
             OR deskripsi_kegiatan LIKE '%$keyword%' 
             OR devisi LIKE '%$keyword%'";

$result = $conn->query($query);

$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><input type='checkbox' name='selected_ids[]' value='".$row['id']."'></td>";
    echo "<td>".$no++."</td>";
    echo "<td>".$row['akun']."</td>";
    echo "<td>".$row['deskripsi']."</td>";
    echo "<td>".$row['tanggal']."</td>";
    echo "<td>".$row['deskripsi_kegiatan']."</td>";
    echo "<td>Rp ".number_format($row['realisasi'], 0, ',', '.')."</td>";
    echo "<td>".$row['devisi']."</td>";
    echo "<td class='action-icons'>
            <i class='fas fa-edit'></i>
            <i class='fas fa-trash'></i>
          </td>";
    echo "</tr>";
}
?>
