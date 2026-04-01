<?php

session_start();
include '../includes/functions.php';
include '../includes/config.php';
include 'includes/user_nav.php';


$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin') {
    header('Location: /Training/Washly/Public/Registration/sign_up.php');
    exit();
}

// Get user info
$query = "SELECT full_name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Dashboard stats
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalBookings = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM bookings WHERE user_id = ? AND status='completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$completedServices = $stmt->get_result()->fetch_assoc()['completed'];

$stmt = $conn->prepare("SELECT SUM(price) as spent FROM bookings WHERE user_id = ? AND status='completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalSpent = $stmt->get_result()->fetch_assoc()['spent'] ?? 0;

$loyaltyPoints = floor($totalSpent / 10);

// Get recent bookings with service name
$stmt = $conn->prepare("
    SELECT b.*, s.name AS service_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recentBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard_style.css">

    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
</head>

<body>

    <div class="container mt-5">
        <div class="text-center mb-5" data-aos="fade-down">
            <h3 class="display-5 fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</h3>
            <h6>Your car deserves the best care. Book your next service today!</h6>
        </div>

        <div class="row g-4">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card dash-card card-1">
                    <div class="card-body text-center">
                        <i class="bi bi-journal-check fs-1"></i>
                        <h5>Total Bookings</h5>
                        <p class="fs-4"><?php echo $totalBookings; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card dash-card card-2">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-1"></i>
                        <h5>Completed Services</h5>
                        <p class="fs-4"><?php echo $completedServices; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card dash-card card-3">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-1"></i>
                        <h5>Total Spent</h5>
                        <p class="fs-4">$<?php echo $totalSpent; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card dash-card card-4">
                    <div class="card-body text-center">
                        <i class="bi bi-gem fs-1"></i>
                        <h5>Loyalty Points</h5>
                        <p class="fs-4"><?php echo $loyaltyPoints; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12" data-aos="fade-up">
                <div class="card dash-card card-recent-bookings">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-3"><i class="bi bi-clock-history me-2"></i>Recent Bookings</h4>

                        <?php if (count($recentBookings) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($recentBookings as $booking): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                            <br>
                                            <small><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></small>
                                            <br>
                                            <small>Car Type: <?php echo htmlspecialchars($booking['car_type']); ?></small>
                                        </div>
                                        <span class="badge <?php echo $booking['status'] == 'completed' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center mt-3">No booking yet. Start by booking your first car wash service!</p>
                            <a href="/Training/Washly/user/booking/book-service.php" class="btn btn-outline-secondary mt-2">
                                🚗 Book Now
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <df-messenger
        intent="Washly-Services-Overview"
        chat-title="Washly_Support_Bot"
        agent-id="901e3ce5-381a-4db1-98bc-db62649ef5cf"
        language-code="en"
        css-customization="true">
    </df-messenger>
    
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        AOS.init({
            duration: 1000
        });
   

    <?php 
    include 'assets/js/book-service.js';
    ?>

document.addEventListener('DOMContentLoaded', () => {
    // Load recent bookings
    fetch('booking/get_recent_bookings.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('recentBookingsContainer').innerHTML = html;
        })
        .catch(err => console.error('Failed to load recent bookings:', err));
});
</script>

</body>

</html>