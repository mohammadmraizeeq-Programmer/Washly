<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$booking_id) {
        $_SESSION['message'] = "Invalid request.";
        header("Location: manage_booking.php");
        exit();
    }

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_booking.php");
    exit();
} else {
    header("Location: manage_booking.php");
    exit();
}
