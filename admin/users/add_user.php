<?php
// Correct the include path to your database config file
include '../../includes/config.php';

// Check if the database connection variable exists from config.php
if (!isset($conn)) {
    die("Error: Database connection not found. Please check your includes/config.php file.");
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($full_name && $email && $password && $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Use a mysqli prepared statement for security
        $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $full_name, $email, $phone, $hashed_password, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='alert alert-success'>User added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error adding user: " . mysqli_error($conn) . "</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<div class='alert alert-warning'>Please fill in all required fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | Washly Admin</title>
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
            <h3 class="display-5 text-center mb-4">Add New User</h3>
            <?php echo $message; ?>
            <div class="card p-4">
                <form action="add_user.php" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Add User</button>
                        <a href="index.php" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left me-2"></i>Back to Users</a>
                    </div>
                </form>
            </div>
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