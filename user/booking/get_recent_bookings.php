<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized access.');
}

$user_id = $_SESSION['user_id'];

// Fetch last 5 bookings with service name
$sql = "
    SELECT b.*, s.name AS service_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
    LIMIT 5
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<ul class="list-group">';
    while ($row = $result->fetch_assoc()) {
        // Fix status mapping
        $statusBadge = '';
        switch ($row['status']) {
            case 'pending':
                $statusBadge = '<span class="badge bg-warning">Pending</span>';
                break;
            case 'completed':   // fixed
                $statusBadge = '<span class="badge bg-success">Completed</span>';
                break;
            case 'cancelled':
                $statusBadge = '<span class="badge bg-danger">Cancelled</span>';
                break;
        }

        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        echo '<div>';
        echo '<strong>' . htmlspecialchars($row['service_name']) . '</strong><br>';
        echo htmlspecialchars($row['booking_date']);
        echo '</div>';
        echo $statusBadge;
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No recent bookings.</p>';
}
?>
