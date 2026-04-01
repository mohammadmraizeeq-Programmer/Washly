<?php
// Correctly include your config file, which should establish the database connection
include '../../includes/config.php';

// Check if the database connection variable exists. It's usually named $conn or $link.
if (!isset($conn)) {
    die("Error: Database connection not found. Please check your includes/config.php file.");
}

$message = '';
$user = null;

// Handle form submission for updating
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($id && $full_name && $email && $role) {
        $update_password = !empty($password);
        
        if ($update_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, email = ?, phone = ?, password = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'sssssi', $full_name, $email, $phone, $hashed_password, $role, $id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ssssi', $full_name, $email, $phone, $role, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to index.php after successful update
            header("Location: index.php?status=updated");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Error updating user: " . mysqli_error($conn) . "</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<div class='alert alert-warning'>Please fill in all required fields.</div>";
    }
}

// Fetch user data for pre-filling the form OR refreshing after update
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, phone, role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $message = "<div class='alert alert-warning'>User not found.</div>";
    }
    mysqli_stmt_close($stmt);
} else {
    $message = "<div class='alert alert-warning'>No user ID provided.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Washly Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    <link rel="stylesheet" href="../assets/css/user_management.css">
</head>
<body>

<?php include '../includes/admin_nav.php'; ?>
    
<div class="container my-5" data-aos="fade-down" data-aos-duration="1000">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="display-5 text-center mb-4">Edit User</h3>
            <?php echo $message; ?>
            <?php if ($user): ?>
            <div class="card p-4">
                <form action="edit_user.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Update User</button>
                        <a href="index.php" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left me-2"></i>Back to Users</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>