<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized";
    exit();
}

$search_term = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'All Status';

// Base query
$sql = "SELECT b.id, b.user_id, b.service_id, b.car_type, b.make, b.model, b.license_plate, b.booking_date, b.status, b.total_price, b.special_requests,
                u.full_name AS user_name,
                s.name AS service_name, s.price AS service_price
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN services s ON b.service_id = s.id
        WHERE 1=1";

$params = [];
$types = '';

// Status filter
if ($status_filter !== 'All Status') {
    $sql .= " AND b.status=?";
    $params[] = $status_filter;
    $types .= 's';
}

// Search filter
if (!empty($search_term)) {
    $sql .= " AND (u.full_name LIKE ? OR s.name LIKE ? OR b.license_plate LIKE ?)";
    $search_param = "%$search_term%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

$sql .= " ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$allBookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch add-ons for all bookings
$stmt_addons = $conn->prepare("SELECT aos.name, aos.price FROM booking_add_ons bao JOIN add_on_services aos ON bao.add_on_id = aos.id WHERE bao.booking_id=?");
foreach ($allBookings as &$booking) {
    $stmt_addons->bind_param("i", $booking['id']);
    $stmt_addons->execute();
    $addons_result = $stmt_addons->get_result();
    $booking['add_ons'] = $addons_result->fetch_all(MYSQLI_ASSOC);

    // Recalculate total_price = service + add-ons
    $total = floatval($booking['service_price']);
    foreach ($booking['add_ons'] as $addon) {
        $total += floatval($addon['price']);
    }
    $booking['total_price'] = $total;
}
$stmt_addons->close();

// FIX: Unset the reference variable before the next loop
unset($booking);

// Output cards
foreach ($allBookings as $booking) {
    include 'booking_card.php';
}

$conn->close();
?>