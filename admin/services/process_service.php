<?php
session_start();
header('Content-Type: application/json');
include '../../includes/config.php';
include '../../includes/functions.php';

if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $features = filter_input(INPUT_POST, 'features', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
            $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
            $emoji = filter_input(INPUT_POST, 'emoji', FILTER_SANITIZE_STRING);

            if (!$name || !$description || $price === false || $duration === false || !$emoji) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit();
            }

            try {
                $stmt = $conn->prepare("INSERT INTO services (name, description, features, price, duration, emoji) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssdis", $name, $description, $features, $price, $duration, $emoji);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Service added successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add service.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'edit':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $features = filter_input(INPUT_POST, 'features', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
            $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
            $emoji = filter_input(INPUT_POST, 'emoji', FILTER_SANITIZE_STRING);

            if ($id === false || !$name || !$description || $price === false || $duration === false || !$emoji) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit();
            }

            try {
                $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, features = ?, price = ?, duration = ?, emoji = ? WHERE id = ?");
                $stmt->bind_param("sssdisi", $name, $description, $features, $price, $duration, $emoji, $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Service updated successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update service.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id === false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid ID provided.']);
                exit();
            }
            try {
                $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Service deleted successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete service.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
            
        case 'toggle_status':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $is_active = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_INT);

            if ($id === false || $is_active === false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit();
            }

            try {
                $stmt = $conn->prepare("UPDATE services SET is_active = ? WHERE id = ?");
                $stmt->bind_param("ii", $is_active, $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Service status updated successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update service status.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();