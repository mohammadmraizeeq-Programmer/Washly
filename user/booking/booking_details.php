<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';
include '../includes/user_nav.php'; // Include your user navigation bar

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

// Check if a booking ID is provided in the URL
if (!isset($_GET['booking_id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>No booking ID provided.</div></div>";
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        b.id, b.car_type, b.license_plate, b.make, b.model, b.booking_date, 
        b.special_requests, b.price, b.total_price, b.status,
        s.name AS service_name,
        u.full_name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
    FROM 
        bookings b
    JOIN 
        services s ON b.service_id = s.id
    JOIN 
        users u ON b.user_id = u.id
    WHERE 
        b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$booking = null;
if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
}
$stmt->close();

// If the booking is not found or doesn't belong to the user, display an error
if (!$booking) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Booking not found or you don't have permission to view it.</div></div>";
    exit();
}

// Fetch add-on details for this booking from the new `booking_add_ons` table
$selected_add_ons = [];
$stmt_addons = $conn->prepare("
    SELECT 
        ao.name, ao.price 
    FROM 
        booking_add_ons bao
    JOIN 
        add_on_services ao ON bao.add_on_id = ao.id
    WHERE 
        bao.booking_id = ?
");
$stmt_addons->bind_param("i", $booking_id);
$stmt_addons->execute();
$result_addons = $stmt_addons->get_result();
while ($ao = $result_addons->fetch_assoc()) {
    $selected_add_ons[] = $ao;
}
$stmt_addons->close();

// Separate the date and time from the `booking_date` column
$booking_datetime = new DateTime($booking['booking_date']);
$formatted_date = $booking_datetime->format('Y-m-d');
$formatted_time = $booking_datetime->format('h:i A');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - #<?= htmlspecialchars($booking['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .booking-details-modal {
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .booking-section-title {
            color: #495057;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .booking-item-label {
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="booking-details-modal card p-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold mb-0">Booking Details</h4>
                <p class="text-muted mb-0">Complete information for booking #<?= htmlspecialchars($booking['id']) ?></p>
            </div>
            <span class="badge rounded-pill <?= ($booking['status'] == 'pending') ? 'bg-warning text-dark' : (($booking['status'] == 'Completed') ? 'bg-success' : 'bg-danger') ?>">
                <?= htmlspecialchars(ucfirst($booking['status'])) ?>
            </span>
        </div>

        <div class="mb-4">
            <h5 class="booking-section-title"><i class="bi bi-person-fill me-2"></i> Customer Information</h5>
            <hr class="mt-2 mb-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Name:</span> <span class="fw-bold"><?= htmlspecialchars($booking['customer_name']) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Email:</span> <span class="fw-bold"><?= htmlspecialchars($booking['customer_email']) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Phone:</span> <span class="fw-bold"><?= htmlspecialchars($booking['customer_phone'] ?? 'N/A') ?></span></p>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="booking-section-title"><i class="bi bi-gear-fill me-2"></i> Service Information</h5>
            <hr class="mt-2 mb-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Service:</span> <span class="fw-bold"><?= htmlspecialchars($booking['service_name']) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Price:</span> <span class="fw-bold text-success">$<?= number_format($booking['price'], 2) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Date:</span> <span class="fw-bold"><?= htmlspecialchars($formatted_date) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Time:</span> <span class="fw-bold"><?= htmlspecialchars($formatted_time) ?></span></p>
                </div>
            </div>
            
            <?php if (!empty($selected_add_ons)): ?>
                <div class="mt-3">
                    <h6 class="booking-section-title small mb-2">Add-On Services</h6>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($selected_add_ons as $ao): ?>
                            <li>
                                <span class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bi bi-plus-circle me-1"></i> <?= htmlspecialchars($ao['name']) ?></span>
                                    <span class="fw-bold text-success">+ $<?= number_format($ao['price'], 2) ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <h5 class="booking-section-title"><i class="bi bi-car-front-fill me-2"></i> Vehicle Information</h5>
            <hr class="mt-2 mb-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">Vehicle:</span> <span class="fw-bold"><?= htmlspecialchars($booking['make']) ?> <?= htmlspecialchars($booking['model']) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="booking-item-label">License Plate:</span> <span class="fw-bold"><?= htmlspecialchars($booking['license_plate']) ?></span></p>
                </div>
            </div>
        </div>

        <div>
            <h5 class="booking-section-title"><i class="bi bi-sticky-fill me-2"></i> Special Notes</h5>
            <hr class="mt-2 mb-3">
            <div class="p-3 bg-light rounded">
                <p class="text-muted mb-0"><?= !empty($booking['special_requests']) ? nl2br(htmlspecialchars($booking['special_requests'])) : 'No special requests were noted.' ?></p>
            </div>
        </div>

        <div class="mt-4">
            <div class="d-flex justify-content-end align-items-center">
                <h5 class="fw-bold mb-0 me-3">Total Price:</h5>
                <h4 class="fw-bold text-success mb-0">$<?= number_format($booking['total_price'], 2) ?></h4>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="booking_history.php" class="btn btn-secondary">Close</a>
            <a href="edit_booking.php?booking_id=<?= htmlspecialchars($booking['id']) ?>" class="btn btn-primary">Edit Booking</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>