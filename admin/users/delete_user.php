<?php

include '../../includes/config.php';

// Check if the database connection variable exists from config.php
if (!isset($conn)) {
    die("Error: Database connection not found. Please check your includes/config.php file.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    // 2. Use a prepared statement with mysqli to prevent SQL injection
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Redirect back to the user list, regardless of success or failure
header("Location: index.php");
exit();
?>