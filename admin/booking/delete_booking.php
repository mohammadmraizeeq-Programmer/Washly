<?php
session_start();
include '../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$booking_id = $_POST['booking_id'];

// Start a transaction
$conn->begin_transaction();

try {
    // First, delete related add-ons
    $stmt_addons = $conn->prepare("DELETE FROM booking_add_ons WHERE booking_id = ?");
    $stmt_addons->bind_param("i", $booking_id);
    $stmt_addons->execute();
    $stmt_addons->close();

    // Then, delete the booking
    $stmt_booking = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt_booking->bind_param("i", $booking_id);
    $stmt_booking->execute();
    $stmt_booking->close();

    // Commit the transaction
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // An error occurred; roll back the transaction
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>  