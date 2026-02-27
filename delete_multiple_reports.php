<?php
include 'config.php';

if (isset($_POST['ids'])) {
    $ids = $_POST['ids'];
    
    // Membuat placeholder untuk setiap id
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("DELETE FROM laporan WHERE id IN ($placeholders)");

    // Bind parameters
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'no_ids';
}
?>
