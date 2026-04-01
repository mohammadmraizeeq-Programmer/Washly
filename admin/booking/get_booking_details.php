<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$booking_id = $_GET['id'] ?? 0;

if (!$booking_id) {
    echo json_encode(['error' => 'Booking ID is required']);
    exit();
}

$sql = "SELECT b.id, b.user_id, b.service_id, b.car_type, b.make, b.model, b.license_plate, b.booking_date, b.status, b.total_price, b.special_requests,
                u.full_name AS user_name, u.email AS user_email, u.phone AS user_phone,
                s.name AS service_name, s.price AS service_price
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN services s ON b.service_id = s.id
        WHERE b.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if ($booking) {
    // Fetch add-ons
    $stmt_addons = $conn->prepare("SELECT aos.name, aos.price FROM booking_add_ons bao JOIN add_on_services aos ON bao.add_on_id = aos.id WHERE bao.booking_id=?");
    $stmt_addons->bind_param("i", $booking['id']);
    $stmt_addons->execute();
    $addons_result = $stmt_addons->get_result();
    $booking['add_ons'] = $addons_result->fetch_all(MYSQLI_ASSOC);
    $stmt_addons->close();

    echo json_encode($booking);
} else {
    echo json_encode(['error' => 'Booking not found']);
}

$conn->close();
?>