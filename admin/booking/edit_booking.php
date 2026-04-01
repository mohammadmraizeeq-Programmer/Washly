<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

// Get booking if editing
$booking_id = $_GET['id'] ?? null;
$booking = null;

if ($booking_id) {
    $stmt = $conn->prepare("SELECT b.*, u.full_name AS user_name FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id=?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $user_id = $_POST['user_id'];
    $service_id = $_POST['service_id'];
    $car_type = $_POST['car_type'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $license_plate = $_POST['license_plate'];
    $booking_date = $_POST['booking_date'];
    $status = $_POST['status'];
    $special_requests = $_POST['special_requests'] ?? '';
    $selected_addons = $_POST['add_ons'] ?? [];

    // Calculate total price with a more robust method
    $total_price = 0;

    // Get Service Price
    $stmt_service = $conn->prepare("SELECT price FROM services WHERE id=?");
    $stmt_service->bind_param("i", $service_id);
    $stmt_service->execute();
    $result_service = $stmt_service->get_result();
    if ($result_service->num_rows > 0) {
        $service_price = $result_service->fetch_assoc()['price'];
        $total_price += $service_price;
    }
    $stmt_service->close();
    
    // Get Add-ons Price
    if (!empty($selected_addons)) {
        $placeholders = implode(',', array_fill(0, count($selected_addons), '?'));
        $types = str_repeat('i', count($selected_addons));
        $stmt_addons_price = $conn->prepare("SELECT SUM(price) as addons_total FROM add_on_services WHERE id IN ($placeholders)");
        $stmt_addons_price->bind_param($types, ...$selected_addons);
        $stmt_addons_price->execute();
        $result_addons = $stmt_addons_price->get_result();
        if ($result_addons->num_rows > 0) {
            $addons_total = $result_addons->fetch_assoc()['addons_total'];
            $total_price += $addons_total;
        }
        $stmt_addons_price->close();
    }
    
    if ($booking_id) {
        // Update booking
        $stmt = $conn->prepare("UPDATE bookings SET user_id=?, service_id=?, car_type=?, make=?, model=?, license_plate=?, booking_date=?, status=?, special_requests=?, total_price=? WHERE id=?");
        $stmt->bind_param("iissssssdii", $user_id, $service_id, $car_type, $make, $model, $license_plate, $booking_date, $status, $special_requests, $total_price, $booking_id);
        $stmt->execute();
        $stmt->close();

        // Update add-ons
        $conn->query("DELETE FROM booking_add_ons WHERE booking_id=$booking_id");
        if (!empty($selected_addons)) {
            foreach ($selected_addons as $addon_id) {
                $stmt = $conn->prepare("INSERT INTO booking_add_ons (booking_id, add_on_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $booking_id, $addon_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    } else {
        // Insert new booking
        $status = 'Pending'; // Default status for new bookings
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, car_type, make, model, license_plate, booking_date, status, special_requests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssssssd", $user_id, $service_id, $car_type, $make, $model, $license_plate, $booking_date, $status, $special_requests, $total_price);
        $stmt->execute();
        $new_booking_id = $stmt->insert_id;
        $stmt->close();

        // Insert add-ons
        if (!empty($selected_addons)) {
            foreach ($selected_addons as $addon_id) {
                $stmt = $conn->prepare("INSERT INTO booking_add_ons (booking_id, add_on_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $new_booking_id, $addon_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    header("Location: manage_booking.php");
    exit();
}

// Fetch users, services, add-ons
$users = $conn->query("SELECT id, full_name FROM users")->fetch_all(MYSQLI_ASSOC);
$services = $conn->query("SELECT id, name, price FROM services")->fetch_all(MYSQLI_ASSOC);
$addons = $conn->query("SELECT id, name, price FROM add_on_services")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $booking ? "Edit Booking" : "Add Booking" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_nav_style.css">
    <link rel="stylesheet" href="../assets/css/edit_booking.css"> </head>

<body>
    <div class="container my-5" data-aos="fade-up" data-aos-duration="800">
        <div class="form-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="page-title">
                <?= $booking ? "Edit Booking" : "Add Booking" ?>
                <?php if ($booking): ?>
                    <small class="text-muted" style="font-size: 0.7em;">for <?= htmlspecialchars($booking['user_name']) ?></small>
                <?php endif; ?>
            </h3>
            <?php if ($booking): ?>
                <div class="dropdown status-action-dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-id="<?= $booking['id'] ?>">
                        <?= htmlspecialchars($booking['status']) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item status-option" href="#" data-status="Pending" data-id="<?= $booking['id'] ?>">Pending</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="Confirmed" data-id="<?= $booking['id'] ?>">Confirmed</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="In-Progress" data-id="<?= $booking['id'] ?>">In Progress</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="Completed" data-id="<?= $booking['id'] ?>">Completed</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="Cancelled" data-id="<?= $booking['id'] ?>">Cancelled</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="booking-form-card" data-aos="zoom-in" data-aos-duration="600">
            <form method="POST" id="bookingForm">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? '' ?>">
                <input type="hidden" name="status" value="<?= $booking['status'] ?? 'Pending' ?>">

                <h6 class="form-section-title"><i class="bi bi-person-fill"></i> Client & Service Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= $booking && $booking['user_id'] == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="service_id" class="form-label">Service</label>
                        <select name="service_id" id="service_id" class="form-select" required>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>" data-price="<?= $service['price'] ?>" <?= $booking && $booking['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['name']) ?> - <?= $service['price'] ?> JD
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h6 class="form-section-title"><i class="bi bi-car-front-fill"></i> Vehicle Information</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="car_type" class="form-label">Car Type</label>
                        <input type="text" class="form-control" id="car_type" name="car_type" value="<?= $booking['car_type'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="make" class="form-label">Make</label>
                        <input type="text" class="form-control" id="make" name="make" value="<?= $booking['make'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" value="<?= $booking['model'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="license_plate" class="form-label">License Plate</label>
                    <input type="text" class="form-control" id="license_plate" name="license_plate" value="<?= $booking['license_plate'] ?? '' ?>" required>
                </div>

                <h6 class="form-section-title"><i class="bi bi-calendar-check"></i> Scheduling & Add-ons</h6>
                <div class="mb-3">
                    <label for="booking_date" class="form-label">Booking Date & Time</label>
                    <input type="datetime-local" class="form-control" id="booking_date" name="booking_date" value="<?= $booking['booking_date'] ? date('Y-m-d\TH:i', strtotime($booking['booking_date'])) : '' ?>" required>
                </div>

                <div class="mb-3">
                    <label for="add_ons" class="form-label">Add-Ons</label>
                    <div class="d-flex flex-wrap">
                        <?php foreach ($addons as $addon): ?>
                            <?php
                            $checked = '';
                            if ($booking_id) {
                                $stmt = $conn->prepare("SELECT * FROM booking_add_ons WHERE booking_id=? AND add_on_id=?");
                                $stmt->bind_param("ii", $booking_id, $addon['id']);
                                $stmt->execute();
                                $checked = $stmt->get_result()->num_rows ? 'checked' : '';
                                $stmt->close();
                            }
                            ?>
                            <div class="form-check me-3">
                                <input class="form-check-input addon-checkbox" type="checkbox" name="add_ons[]" value="<?= $addon['id'] ?>" data-price="<?= $addon['price'] ?>" <?= $checked ?>>
                                <label class="form-check-label"><?= $addon['name'] ?> - <?= $addon['price'] ?> JD</label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="special_requests" class="form-label">Special Requests</label>
                    <textarea name="special_requests" id="special_requests" class="form-control" rows="3"><?= $booking['special_requests'] ?? '' ?></textarea>
                </div>

                <div class="total-price-section">
                    <h5>Total Price: <span id="totalPrice"><?= $booking['total_price'] ?? '0' ?></span> JD</h5>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> <?= $booking ? "Update Booking" : "Add Booking" ?></button>
                    <a href="manage_booking.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="../assets/js/manage_bookings.js"></script>
    <script src="../assets/js/edit_booking_script.js"></script>

</body>

</html>