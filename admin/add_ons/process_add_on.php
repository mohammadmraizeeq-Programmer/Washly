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
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

            if (!$name || $price === false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit();
            }

            try {
                $stmt = $conn->prepare("INSERT INTO add_on_services (name, description, price) VALUES (?, ?, ?)");
                $stmt->bind_param("ssd", $name, $description, $price);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Add-on added successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add add-on.']);
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
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

            if ($id === false || !$name || $price === false) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit();
            }

            try {
                $stmt = $conn->prepare("UPDATE add_on_services SET name = ?, description = ?, price = ? WHERE id = ?");
                $stmt->bind_param("ssdi", $name, $description, $price, $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Add-on updated successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update add-on.']);
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
                $stmt = $conn->prepare("DELETE FROM add_on_services WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Add-on deleted successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete add-on.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'toggle_status':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $is_active = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_INT);

            if ($id === false || ($is_active !== 0 && $is_active !== 1)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data provided for status toggle.']);
                exit();
            }

            try {
                $stmt = $conn->prepare("UPDATE add_on_services SET is_active = ? WHERE id = ?");
                $stmt->bind_param("ii", $is_active, $id);
                if ($stmt->execute()) {
                    $status_message = ($is_active == 1) ? 'enabled' : 'disabled';
                    echo json_encode(['success' => true, 'message' => 'Add-on status has been ' . $status_message . ' successfully.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to toggle add-on status.']);
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