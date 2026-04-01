<?php
session_start();
include '../../includes/config.php'; // DB connection

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize defaults
$name = htmlspecialchars($_SESSION['full_name'] ?? 'Customer');
$service = 'N/A';
$car_type = 'N/A';
$date_display = 'N/A';
$time_display = 'N/A';
$service_price = '0.00';
$total_price = '0.00';
$special_requests = 'N/A';
$add_ons = [];

// Fetch latest booking for this user
$sql = "SELECT b.*, s.name AS service_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE b.user_id = ?
        ORDER BY b.id DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error (prepare): " . $conn->error);
}

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();

        $service = htmlspecialchars($booking['service_name']);
        $car_type = htmlspecialchars($booking['car_type']);
        $service_price = htmlspecialchars($booking['price']);   // <-- use bookings.price
        $total_price = htmlspecialchars($booking['total_price']);
        $special_requests = htmlspecialchars($booking['special_requests'] ?? 'N/A');

        // Handle datetime
        if (!empty($booking['booking_date'])) {
            $datetime_obj = new DateTime($booking['booking_date']);
            $date_display = $datetime_obj->format('Y-m-d');
            $time_display = $datetime_obj->format('h:i A');
        }

        // Fetch add-ons for this booking
        $sql_addons = "SELECT a.name, a.price 
                       FROM booking_add_ons ba
                       JOIN add_ons a ON ba.add_on_id = a.id
                       WHERE ba.booking_id = ?";
        $stmt_addons = $conn->prepare($sql_addons);
        if ($stmt_addons) {
            $stmt_addons->bind_param("i", $booking['id']);
            $stmt_addons->execute();
            $result_addons = $stmt_addons->get_result();
            while ($row = $result_addons->fetch_assoc()) {
                $add_ons[] = [
                    'name' => htmlspecialchars($row['name']),
                    'price' => htmlspecialchars($row['price'])
                ];
            }
            $stmt_addons->close();
        }
    }
} else {
    die("SQL Error (execute): " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Booking Confirmed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/confirm_booking.css">
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card text-center">
        <div class="card-header">
          <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754;"></i>
          <h2 class="card-title mt-3">Booking Confirmed!</h2>
        </div>
        <div class="card-body">
          <p class="card-text lead">Thank you, <strong><?php echo $name; ?></strong>!</p>
          <p class="card-text text-muted">Your booking has been successfully received.</p>

          <ul class="list-group list-group-flush text-start mt-4">
            <li class="list-group-item"><strong>Service:</strong> <?php echo $service; ?></li>
            <li class="list-group-item"><strong>Car Type:</strong> <?php echo $car_type; ?></li>
            <li class="list-group-item"><strong>Date:</strong> <?php echo $date_display; ?></li>
            <li class="list-group-item"><strong>Time:</strong> <?php echo $time_display; ?></li>
            <?php if ($special_requests !== 'N/A' && !empty($special_requests)): ?>
              <li class="list-group-item"><strong>Special Requests:</strong> <?php echo $special_requests; ?></li>
            <?php endif; ?>
          </ul>

          <div class="card-body text-start">
            <h5 class="fw-bold">Price Details</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between">
                <span>Base Service Price:</span>
                <span><?php echo $service_price; ?> JD</span>
              </li>
              <?php if (!empty($add_ons)): ?>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Add-On Services:</span>
                </li>
                <?php foreach ($add_ons as $addon): ?>
                  <li class="list-group-item d-flex justify-content-between ps-4">
                    <span class="text-muted"><i class="bi bi-plus me-2"></i><?php echo $addon['name']; ?></span>
                    <span class="text-muted"><?php echo $addon['price']; ?> JD</span>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold mt-2">
              <span>Total Price:</span>
              <span style="font-size: 1.25rem; color: #3498db;"><?php echo $total_price; ?> JD</span>
            </div>
          </div>

          <div class="mt-4">
            <a href="/Training/Washly/user/" class="back-link"> &larr; Back to Home </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
