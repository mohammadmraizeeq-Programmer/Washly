<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$full_name = "Mohammad";
$email = "mohammad@bmw.com";
$password = "123456";
$role = "admin";

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Corrected column name to 'full_name' to match your previous code
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $full_name, $email, $password_hash, $role);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>