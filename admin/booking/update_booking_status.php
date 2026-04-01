<?php
session_start();
include '../../includes/config.php';

header('Content-Type: application/json');

// Check for admin role and valid request method
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid request.']);
    exit();
}

// Check for a valid database connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get and sanitize POST data
$bookingId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$newStatus = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

// Validate data
if (!$bookingId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID or status.']);
    exit();
}

// Allowed statuses based on DB ENUM
$allowedStatuses = ['pending', 'completed', 'cancelled'];
if (!in_array($newStatus, $allowedStatuses, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit();
}

// Prepare SQL statement to update the booking status
$sql = "UPDATE bookings SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("si", $newStatus, $bookingId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking status updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update booking status: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
