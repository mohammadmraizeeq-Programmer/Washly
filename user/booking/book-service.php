<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../includes/config.php';
include '../../includes/functions.php';
include '../includes/user_nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

// Initialize variables to hold user data
$user_name = '';
$user_email = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Prepare and execute a query to get user details from the 'users' table
    $stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $user_name = $user_data['full_name'];
        $user_email = $user_data['email'];
    }
    $stmt->close();
}

// Fetch add-on services from the database
$add_ons = [];
$stmt_add_ons = $conn->prepare("SELECT * FROM add_on_services WHERE is_active = 1 ORDER BY price ASC");
$stmt_add_ons->execute();
$result_add_ons = $stmt_add_ons->get_result();
while ($ao = $result_add_ons->fetch_assoc()) {
    $add_ons[] = $ao;
}
$stmt_add_ons->close();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link href="../assets/css/bookService_style.css" rel="stylesheet">

<div class="book-service-content">
    <div class="container my-5">
        <div class="text-center" data-aos="fade-up">
            <h3 class="display-5 fw-bold mb-2">Book Your Car Service</h3>
            <h6 class="text-muted">Choose from our premium car wash and detailing services</h6>
        </div>

        <div class="row mt-5">
            <div class="col-lg-8">
                <div class="book-steps mb-5">
                    <div class="step-item active" id="step-1-header">
                        <div class="step-number"><span class="step-num-text">1</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Car Type</div>
                    </div>
                    <div class="step-item" id="step-2-header">
                        <div class="step-number"><span class="step-num-text">2</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Service</div>
                    </div>
                    <div class="step-item" id="step-3-header">
                        <div class="step-number"><span class="step-num-text">3</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Time & Date</div>
                    </div>
                    <div class="step-item" id="step-4-header">
                        <div class="step-number"><span class="step-num-text">4</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Location</div>
                    </div>
                    <div class="step-item" id="step-5-header">
                        <div class="step-number"><span class="step-num-text">5</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Customer Info</div>
                    </div>
                    <div class="step-item" id="step-6-header">
                        <div class="step-number"><span class="step-num-text">6</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Vehicle Info</div>
                    </div>
                    <div class="step-item" id="step-7-header">
                        <div class="step-number"><span class="step-num-text">7</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Add-ons</div>
                    </div>
                    <div class="step-item" id="step-8-header">
                        <div class="step-number"><span class="step-num-text">8</span><i class="bi bi-check-circle-fill" style="display: none;"></i></div>
                        <div class="step-label">Requests</div>
                    </div>
                </div>

                <form id="bookingForm" action="save_booking.php" method="POST">
                    <div id="step1" class="step-content current-step">
                        <h5 class="mb-4" data-aos="fade-right">1. Select Your Car Type</h5>
                        <div class="d-flex flex-wrap gap-3 justify-content-center" id="carTypeButtons">
                            <?php
                            $carTypes = ['Sedan' => '🚗', 'SUV' => '🚙', 'Pickup' => '🛻', 'Coupe' => '🏎️', 'Van' => '🚐'];
                            foreach ($carTypes as $type => $emoji) {
                                echo '<button type="button" class="btn car-select-btn" data-car="' . $type . '" data-aos="fade-up">
                                         <span class="car-emoji-icon">' . $emoji . '</span>
                                         <span class="mt-2">' . $type . '</span>
                                     </button>';
                            }
                            ?>
                        </div>
                    </div>

                    <div id="step2" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">2. Choose Your Service</h5>
                        <div class="row g-4 justify-content-center">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM services ORDER BY created_at ASC");
                            $stmt->execute();
                            $services = $stmt->get_result();
                            while ($s = $services->fetch_assoc()) {
                                $is_active = $s['is_active'];
                                $features = json_decode($s['features'], true);
                                echo '<div class="col-md-5" data-aos="fade-up" data-service-id="' . htmlspecialchars($s['id']) . '" data-name="' . htmlspecialchars($s['name']) . '" data-price="' . htmlspecialchars($s['price']) . '" data-duration="' . htmlspecialchars($s['duration']) . '">
                                         <div class="card service-card position-relative">';
                                if ($is_active == 0) {
                                    echo '<span class="unavailable-badge badge rounded-pill bg-danger">Unavailable</span>';
                                }
                                echo '<span class="service-time">' . $s['duration'] . ' min</span>
                                         <div class="card-body text-center">
                                             <div class="service-icon mb-3">' . $s['emoji'] . '</div>
                                             <h5 class="card-title fw-bold">' . $s['name'] . '</h5>
                                             <p class="card-text text-muted mb-2">' . $s['description'] . '</p>
                                             <p class="fs-4 fw-bold text-accent">' . $s['price'] . ' JD</p>
                                             <ul class="text-start ps-3 list-unstyled">';
                                foreach ($features as $f) {
                                    echo '<li><i class="bi bi-check-circle-fill text-success me-2"></i>' . $f . '</li>';
                                }
                                echo '</ul>';
                                if ($is_active == 1) {
                                    echo '<button type="button" class="service-select-btn btn btn-primary w-100 fw-bold">Select</button>';
                                } else {
                                    echo '<button type="button" class="service-select-btn btn btn-primary w-100 fw-bold" disabled>Not Available</button>';
                                }
                                echo '</div>
                                         </div>
                                     </div>';
                            }
                            $stmt->close();
                            ?>
                        </div>
                    </div>

                    <div id="step3" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">3. Select Date and Time</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card p-4 shadow-sm booking-card" data-aos="fade-up">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h6 class="fw-bold mb-2">Choose a Date</h6>
                                            <input type="date" class="form-control booking-input" id="bookingDate">
                                            <div id="dateWarning" class="alert alert-danger mt-2" role="alert" style="display: none;">
                                                You can't book a service for a past date.
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <h6 class="fw-bold mb-2">Choose a Time Slot</h6>
                                            <select class="form-select booking-input" id="bookingTime">
                                                <option selected disabled>Select a time...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary back-btn" data-step="2">Back: Choose Service</button>
                            <button type="button" id="bookNowBtn" class="btn btn-primary fw-bold book-now-btn" disabled>Next: Select Location</button>
                        </div>
                    </div>

                    <div id="step4" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">4. Select Your Location</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card p-3 shadow-sm" data-aos="fade-up">
                                    <div class="card-body text-center">
                                        <p class="text-muted">Move the map around to see your location.</p>
                                        <iframe
                                            width="100%"
                                            height="300"
                                            style="border:0"
                                            loading="lazy"
                                            allowfullscreen src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3454.123456789!2d35.930359!3d31.963158!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151c6c9f1234567%3A0xabcdef123456789!2sAmman%2C+Jordan!5e0!3m2!1sen!2sjo!4v1690000000000!5m2!1sen!2sjo">
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary back-btn" data-step="3">Back: Time & Date</button>
                            <button type="button" id="confirmLocationBtn" class="btn btn-primary fw-bold">Next: Customer Info</button>
                        </div>
                    </div>

                    <div id="step5" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">5. Customer Information</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card p-4 shadow-sm booking-card" data-aos="fade-up">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="customerName" class="form-label fw-bold">Full Name</label>
                                            <input type="text" class="form-control booking-input" id="customerName" name="customer_name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="customerEmail" class="form-label fw-bold">Email Address</label>
                                            <input type="email" class="form-control booking-input" id="customerEmail" name="customer_email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="customerPhone" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control booking-input" id="customerPhone" name="customer_phone" placeholder="e.g., +962 7xx xxxx xx" required>
                                        </div>
                                        <input type="hidden" name="car_type" id="hiddenCarType">
                                        <input type="hidden" name="service_id" id="hiddenServiceId">
                                        <input type="hidden" name="service_price" id="hiddenServicePrice">
                                        <input type="hidden" name="booking_date" id="hiddenBookingDate">
                                        <input type="hidden" name="booking_time" id="hiddenBookingTime">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary back-btn" data-step="4">Back: Location</button>
                            <button type="button" id="nextToVehicleInfoBtn" class="btn btn-primary fw-bold" disabled>Next: Vehicle Info</button>
                        </div>
                    </div>

                    <div id="step6" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">6. Vehicle Information</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card p-4 shadow-sm booking-card" data-aos="fade-up">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="vehicleMake" class="form-label fw-bold">Make</label>
                                            <input type="text" class="form-control booking-input" id="vehicleMake" name="make" placeholder="e.g., Toyota" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="vehicleModel" class="form-label fw-bold">Model</label>
                                            <input type="text" class="form-control booking-input" id="vehicleModel" name="model" placeholder="e.g., Camry" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="licensePlate" class="form-label fw-bold">License Plate</label>
                                            <input type="text" class="form-control booking-input" id="licensePlate" name="license_plate" placeholder="e.g., 12-34567" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary back-btn" data-step="5">Back: Customer Info</button>
                            <button type="button" id="nextToAddonsBtn" class="btn btn-primary fw-bold" disabled>Next: Add-On Services</button>
                        </div>
                    </div>

                    <div id="step7" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">7. Add-On Services</h5>
                        <h6 class="text-muted mb-4" data-aos="fade-right">Enhance your service with additional treatments</h6>
                        <div class="row g-4 justify-content-center" id="addOnsContainer">
                            <?php foreach ($add_ons as $ao): ?>
                                <div class="col-md-5" data-aos="fade-up" data-add-on-id="<?= htmlspecialchars($ao['id']); ?>" data-name="<?= htmlspecialchars($ao['name']); ?>" data-price="<?= htmlspecialchars($ao['price']); ?>">
                                    <div class="card add-on-card">
                                        <div class="card-body">
                                            <div class="form-check d-flex align-items-center mb-3">
                                                <input class="form-check-input add-on-checkbox" type="checkbox" value="<?= htmlspecialchars($ao['id']); ?>" id="add-on-<?= htmlspecialchars($ao['id']); ?>">
                                                <label class="form-check-label ms-3 w-100" for="add-on-<?= htmlspecialchars($ao['id']); ?>">
                                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($ao['name']); ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($ao['description']); ?></small>
                                                </label>
                                                <span class="ms-auto fw-bold text-accent">+<?= htmlspecialchars($ao['price']); ?> JD</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary back-btn" data-step="6">Back: Vehicle Info</button>
                            <button type="button" id="nextToRequestsBtn" class="btn btn-primary fw-bold">Next: Special Requests</button>
                        </div>
                    </div>

                    <div id="step8" class="step-content">
                        <h5 class="mb-4" data-aos="fade-right">8. Special Requests</h5>
                        <h6 class="text-muted mb-4" data-aos="fade-right">Tell us about any specific instructions, problem areas, or preferences.</h6>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card p-4 shadow-sm booking-card" data-aos="fade-up">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="specialRequests" class="form-label fw-bold">Special Requests</label>
                                            <textarea class="form-control booking-input" id="specialRequests" name="special_requests" rows="5" placeholder="e.g., 'Please pay special attention to the front wheels' or 'Be careful with the paint on the passenger side door.'"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary back-btn" data-step="7">Back: Add-On Services</button>
                            <button type="submit" id="confirmBookingBtn" class="btn btn-primary fw-bold">Confirm Booking</button>
                        </div>
                    </div>

                    <div style="display: none;">
                        <input type="hidden" name="make" id="hiddenMake">
                        <input type="hidden" name="model" id="hiddenModel">
                        <input type="hidden" name="license_plate" id="hiddenLicensePlate">
                        <input type="hidden" name="special_requests" id="hiddenSpecialRequests">

                        <div id="addonsFormGroup"></div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 d-none d-lg-block">
                <div class="booking-summary-card">
                    <div class="p-4 shadow-sm h-100">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Booking Summary
                        </h4>
                        <div class="summary-details-card p-3 mb-4 rounded-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-check me-2 fs-5"></i>
                                <span id="summary-datetime-date" class="fw-bold">...</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2 fs-5"></i>
                                <span id="summary-datetime-time" class="fw-bold">...</span>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-car-front-fill me-2 fs-5"></i>
                                <span id="summary-car-type" class="fw-bold">...</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-box-seam-fill me-2 fs-5"></i>
                                <span id="summary-service" class="fw-bold">...</span>
                            </div>
                            <hr>
                            <div class="summary-add-ons-section" style="display: none;">
                                <h6 class="fw-bold mb-2">Add-ons</h6>
                                <ul id="summary-add-ons-list" class="list-unstyled mb-2">
                                </ul>
                                <hr>
                            </div>
                            <div class="total-section d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Total:</h5>
                                <h5 class="fw-bold text-accent mb-0" id="summary-total-price">...</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/book-service.js"></script>
</body>

</html>