<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../includes/config.php';  // DB connection
include '../../includes/functions.php';  // if needed

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: book_service.php');
    exit();
}

// Helper sanitize function
function sanitize($str) {
    return trim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}

// Collect inputs
$user_id        = $_SESSION['user_id'];
$car_type       = isset($_POST['car_type']) ? sanitize($_POST['car_type']) : null;
$service_id     = isset($_POST['service_id']) ? intval($_POST['service_id']) : null;
$service_price  = isset($_POST['service_price']) ? floatval($_POST['service_price']) : 0.0;
$booking_date   = isset($_POST['booking_date']) ? sanitize($_POST['booking_date']) : null;
$booking_time   = isset($_POST['booking_time']) ? sanitize($_POST['booking_time']) : null;
$make           = isset($_POST['make']) ? sanitize($_POST['make']) : '';
$model          = isset($_POST['model']) ? sanitize($_POST['model']) : '';
$license_plate  = isset($_POST['license_plate']) ? sanitize($_POST['license_plate']) : '';
$special_requests = isset($_POST['special_requests']) ? sanitize($_POST['special_requests']) : '';

$selected_add_ons = isset($_POST['add_ons']) && is_array($_POST['add_ons'])
    ? array_map('intval', $_POST['add_ons'])
    : [];

// Validation
$errors = [];
if (empty($car_type)) $errors[] = "Car type is required.";
if (empty($service_id)) $errors[] = "Service is required.";
if (empty($booking_date)) $errors[] = "Booking date is required.";
if (empty($booking_time)) $errors[] = "Booking time is required.";
if (empty($make) || empty($model) || empty($license_plate)) {
    $errors[] = "Vehicle make, model, and license plate are required.";
}

if (!empty($errors)) {
    echo '<h3>Errors:</h3><ul>';
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo '</ul>';
    exit();
}

// Combine date & time
$full_booking_datetime = $booking_date . ' ' . $booking_time;

// Get service details
$service_details = ['duration' => 0, 'name' => ''];
$stmt = $conn->prepare("SELECT duration, name FROM services WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        $service_details = $row;
    }
    $stmt->close();
}
$duration = intval($service_details['duration']);
$service_name = $service_details['name'];

// Start total with base service price
$total_price = $service_price;

// Fetch add-on details from add_ons table
$add_on_details = [];
if (!empty($selected_add_ons)) {
    $placeholders = implode(',', array_fill(0, count($selected_add_ons), '?'));
    $types = str_repeat('i', count($selected_add_ons));
    $sql = "SELECT id, name, price FROM add_ons WHERE id IN ($placeholders)";
    $stmt2 = $conn->prepare($sql);
    if ($stmt2) {
        $stmt2->bind_param($types, ...$selected_add_ons);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($ao = $res2->fetch_assoc()) {
            $add_on_details[] = $ao;
            $total_price += floatval($ao['price']);
        }
        $stmt2->close();
    }
}

// Insert booking
$stmtInsert = $conn->prepare("
    INSERT INTO bookings 
      (user_id, service_id, car_type, make, model, license_plate, booking_date, status, price, total_price, duration, special_requests)
    VALUES
      (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)
");
if (!$stmtInsert) {
    die("Prepare failed: " . $conn->error);
}

$stmtInsert->bind_param(
    "iissssssdis",
    $user_id,
    $service_id,
    $car_type,
    $make,
    $model,
    $license_plate,
    $full_booking_datetime,
    $service_price,
    $total_price,
    $duration,
    $special_requests
);

if ($stmtInsert->execute()) {
    $booking_id = $conn->insert_id;

    // Insert add-ons into junction table
    if (!empty($selected_add_ons)) {
        $stmtAddOn = $conn->prepare("INSERT INTO booking_add_ons (booking_id, add_on_id) VALUES (?, ?)");
        if ($stmtAddOn) {
            foreach ($selected_add_ons as $aoid) {
                $stmtAddOn->bind_param("ii", $booking_id, $aoid);
                $stmtAddOn->execute();
            }
            $stmtAddOn->close();
        }
    }

    // Save booking info in session
    $_SESSION['booking_details'] = [
        'booking_id' => $booking_id,
        'service_name' => $service_name,
        'car_type' => $car_type,
        'make' => $make,
        'model' => $model,
        'license_plate' => $license_plate,
        'booking_datetime' => $full_booking_datetime,
        'price' => $service_price,
        'add_ons' => $add_on_details,
        'total_price' => $total_price,
        'special_requests' => $special_requests
    ];

    header('Location: booking_confirmed.php');
    exit();

} else {
    echo "Database insert failed: " . htmlspecialchars($stmtInsert->error);
}

$conn->close();
?>
