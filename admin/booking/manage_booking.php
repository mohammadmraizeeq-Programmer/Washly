<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

include '../includes/admin_nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_nav_style.css">
    <link rel="stylesheet" href="../assets/css/manage_bookings.css">
    <link rel="stylesheet" href="../assets/css/booking_details_modal.css">
</head>

<body>

    <div class="container my-5">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Manage Bookings</h2>
                <p class="text-muted">View and manage all customer appointments</p>
            </div>
            <a href="edit_booking.php" class="btn btn-primary add-booking-btn"><i class="bi bi-plus-lg me-1"></i> Add Booking</a>
        </div>

        <div class="search-filter-row mb-4">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search by customer name, service, or license plate...">
            </div>
            <select id="filterStatusText" class="form-select filter-dropdown">
                <?php
                $statuses = ["All Status", "Pending", "Confirmed", "In-Progress", "Completed", "Cancelled"];
                foreach ($statuses as $status): ?>
                    <option value="<?= $status ?>"><?= $status ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="bookingsList">
        </div>
    </div>

    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" data-aos="zoom-in" data-aos-duration="600">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="bookingDetailsModalLabel">Booking Details</h5>
                    <span class="status-badge" id="modalStatusBadge"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="info-card">
                                <h6 class="info-card-title"><i class="bi bi-person-fill"></i> Client Information</h6>
                                <ul class="list-unstyled info-list">
                                    <li><strong>Name:</strong> <span id="modalUserName"></span></li>
                                    <li><strong>Email:</strong> <span id="modalUserEmail"></span></li>
                                    <li><strong>Phone:</strong> <span id="modalUserPhone"></span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="info-card">
                                <h6 class="info-card-title"><i class="bi bi-wrench"></i> Service Details</h6>
                                <ul class="list-unstyled info-list">
                                    <li><strong>Service:</strong> <span id="modalServiceName"></span></li>
                                    <li><strong>Date:</strong> <span id="modalBookingDate"></span></li>
                                    <li><strong>Time:</strong> <span id="modalBookingTime"></span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="info-card">
                                <h6 class="info-card-title"><i class="bi bi-car-front-fill"></i> Vehicle Details</h6>
                                <ul class="list-unstyled info-list d-flex flex-wrap">
                                    <li class="me-4"><strong>Model:</strong> <span id="modalVehicleInfo"></span></li>
                                    <li><strong>License Plate:</strong> <span id="modalLicensePlate"></span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="info-card">
                                <h6 class="info-card-title"><i class="bi bi-info-circle-fill"></i> Additional Info</h6>
                                <ul class="list-unstyled info-list">
                                    <li><strong>Special Notes:</strong> <span id="modalSpecialNotes"></span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="info-card">
                                <h6 class="info-card-title"><i class="bi bi-currency-dollar"></i> Price & Add-ons</h6>
                                <div class="d-flex justify-content-between align-items-center price-details-row mb-2">
                                    <span>Service Price:</span>
                                    <span class="fw-bold">$<span id="modalServicePrice"></span></span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-success fw-bold">Total Price:</span>
                                    <h5 class="price-text text-success">$<span id="modalTotalFinalPrice"></span></h5>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="modalEditButton" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil-fill me-1"></i>Edit Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="../assets/js/manage_bookings.js"></script>
</body>

</html>