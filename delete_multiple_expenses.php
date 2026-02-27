<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = $_POST['ids'];

    // Log IDs for debugging
    error_log("IDs to delete: " . implode(", ", $ids));

    // Prepare the SQL statement for deletion
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("DELETE FROM pengeluaran WHERE id IN ($placeholders)");

    if ($stmt) {
        // Bind parameters as integers
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        $stmt->execute();
        
        // Check if rows were affected
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang dihapus.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Query preparation failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
}
?>
