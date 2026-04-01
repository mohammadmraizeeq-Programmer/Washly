<?php

// Check if a user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if the logged-in user has an 'admin' role
function is_admin() {
    global $conn; // Access the database connection from the global scope

    if (!is_logged_in()) {
        return false;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        return $user['role'] === 'admin';
    }

    return false;
}

?>