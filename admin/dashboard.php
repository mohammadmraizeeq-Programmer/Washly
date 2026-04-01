<?php
include '../includes/config.php';
include '../includes/functions.php';
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Public/Registration/log_in.php');
    exit();
}
$user_id = $_SESSION['user_id'];

include 'includes/admin_nav.php';

// Get total bookings
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM bookings");
if ($stmt) {
    $stmt->execute();
    $totalBookings = $stmt->get_result()->fetch_assoc()['total'];
} else {
    $totalBookings = 0;
}

// Get today's bookings
$stmt = $conn->prepare("SELECT COUNT(*) AS total_today FROM bookings WHERE DATE(booking_date) = CURDATE()");
if ($stmt) {
    $stmt->execute();
    $todaysBookings = $stmt->get_result()->fetch_assoc()['total_today'];
} else {
    $todaysBookings = 0;
}

// Get total revenue
$stmt = $conn->prepare("SELECT SUM(price) AS total_revenue FROM bookings WHERE status = 'completed'");
if ($stmt) {
    $stmt->execute();
    $totalRevenue = $stmt->get_result()->fetch_assoc()['total_revenue'] ?? 0;
} else {
    $totalRevenue = 0;
}

// Get total pending messages (from the new messages table)
$stmt = $conn->prepare("SELECT COUNT(DISTINCT conversation_id) AS total FROM messages WHERE is_read = 0 AND sender_id != ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $totalMessages = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
} else {
    $totalMessages = 0;
}

$stmt = $conn->prepare("
    SELECT b.*, s.name AS service_name, u.full_name AS user_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.user_id = u.id
    WHERE b.status = 'pending'
    ORDER BY b.booking_date DESC
    LIMIT 10
");
$recentBookings = [];
if ($stmt) {
    $stmt->execute();
    $recentBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin_nav_style.css">
    <link rel="stylesheet" href="assets/css/admin_dashboard_style.css">
</head>

<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert-container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <section id="fleet" class="fleet-section">
        <div class="container mt-5">
            <div class="text-center mb-5" data-aos="fade-down">
                <h3 class="display-5 fw-bold mb-2">Admin Dashboard</h3>
                <p>Manage all bookings and services from here.</p>
            </div>

            <div class="dashboard-section" id="Overview">
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="card dash-card card-1">
                            <div class="card-body text-center">
                                <i class="bi bi-journal-check fs-1"></i>
                                <h5>Total Bookings</h5>
                                <p class="fs-4"><?php echo $totalBookings; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card dash-card card-2">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-day fs-1"></i>
                                <h5>Today's Bookings</h5>
                                <p class="fs-4"><?php echo $todaysBookings; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="card dash-card card-3">
                            <div class="card-body text-center">
                                <i class="bi bi-currency-dollar fs-1"></i>
                                <h5>Total Revenue</h5>
                                <p class="fs-4"><?php echo $totalRevenue; ?> JD</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="card dash-card card-4">
                            <div class="card-body text-center">
                                <i class="bi bi-chat-left-dots fs-1"></i>
                                <h5>Pending Messages</h5>
                                <p class="fs-4"><?php echo $totalMessages; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-12" data-aos="fade-up">
                        <div class="card dash-card card-recent-bookings">
                            <div class="card-body">
                                <h4 class="card-title text-center mb-4">
                                    <i class="bi bi-clock-history me-2"></i>Recent Bookings
                                </h4>
                                <?php if (count($recentBookings) > 0): ?>
                                    <div class="booking-list">
                                        <?php foreach ($recentBookings as $booking): ?>
                                            <div class="booking-item">
                                                <div class="booking-header">
                                                    <span class="booking-service-name"><?php echo htmlspecialchars($booking['service_name']); ?></span>
                                                    <span class="booking-price"><?php echo htmlspecialchars($booking['price']); ?> JD</span>
                                                </div>
                                                <div class="booking-meta">
                                                    <span class="booking-meta-item"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($booking['user_name']); ?></span>
                                                    <span class="booking-meta-item"><i class="bi bi-calendar-check me-1"></i><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                                                    <span class="booking-meta-item"><i class="bi bi-clock me-1"></i><?php echo date('h:i A', strtotime($booking['booking_date'])); ?></span>
                                                    <span class="booking-meta-item"><i class="bi bi-car-front me-1"></i><?php echo htmlspecialchars($booking['car_type']); ?></span>
                                                </div>
                                                <div class="booking-actions">
                                                    <form action="/Training/Washly/admin/booking/process_booking.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                        <input type="hidden" name="action" value="accept">
                                                        <button type="submit" class="btn btn-sm btn-action accept">
                                                            <i class="bi bi-check-lg"></i> Accept
                                                        </button>
                                                    </form>
                                                    <form action="/Training/Washly/admin/booking/process_booking.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-sm btn-action reject">
                                                            <i class="bi bi-x-lg"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">No pending bookings to approve.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="assets/js/nav_scroll.js"></script>
    <script src="assets/js/main_admin.js"></script>
</body>

</html>