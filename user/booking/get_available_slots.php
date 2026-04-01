<?php
include '../../includes/config.php';
include '../../includes/functions.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'available_slots' => []
];

try {
    $date = $_GET['date'] ?? '';
    $service_duration = $_GET['duration'] ?? 0;

    if (empty($date) || !is_numeric($service_duration) || $service_duration <= 0) {
        $response['message'] = 'Invalid date or service duration provided.';
        echo json_encode($response);
        exit();
    }

    $start_time = strtotime('09:00'); 
    $end_time = strtotime('17:00');
    $interval = $service_duration * 60;

    $stmt = $conn->prepare("SELECT booking_date, duration FROM bookings WHERE DATE(booking_date) = ? AND status != 'canceled'");
    if ($stmt === false) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $booked_slots = [];
    while ($row = $result->fetch_assoc()) {
        $booked_slots[] = [
            'start' => strtotime($row['booking_date']),
            'end' => strtotime($row['booking_date']) + ($row['duration'] * 60)
        ];
    }
    $stmt->close();

    $available_slots = [];
    for ($i = $start_time; $i < $end_time; $i += $interval) {
        $slot_start = $i;
        $slot_end = $i + $interval;
        
        if (date('Y-m-d') == $date && $slot_end <= time()) {
            continue;
        }

        $is_booked = false;
        foreach ($booked_slots as $booked) {
            if (($slot_start >= $booked['start'] && $slot_start < $booked['end']) ||
                ($slot_end > $booked['start'] && $slot_end <= $booked['end']) ||
                ($booked['start'] >= $slot_start && $booked['start'] < $slot_end)) {
                $is_booked = true;
                break;
            }
        }

        if (!$is_booked) {
            $available_slots[] = [
                'start_time' => date('H:i', $slot_start),
                'end_time' => date('H:i', $slot_end)
            ];
        }
    }

    $response['success'] = true;
    $response['message'] = 'Slots loaded successfully.';
    $response['available_slots'] = $available_slots;

} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>