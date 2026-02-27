<?php
include('config.php');

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM pengeluaran WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
