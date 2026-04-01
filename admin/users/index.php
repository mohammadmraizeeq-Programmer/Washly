<?php
include '../../includes/config.php';
include '../../includes/functions.php';

if (!isset($conn)) {
    die("Error: Database connection not found. Please check your includes/config.php file.");
}

// Fetch all users from the database using mysqli
$sql = "SELECT id, full_name, email, phone, role, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error fetching users: " . mysqli_error($conn);
    $users = [];
} else {
    // Fetch all results into an array
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

include '../includes/admin_nav.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Washly Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    <link rel="stylesheet" href="../assets/css/user_management.css">
</head>
<body>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down" data-aos-duration="1000">
        <h3 class="display-5">Manage Users</h3>
        <a href="add_user.php" class="btn btn-primary btn-lg">
            <i class="fas fa-user-plus me-2"></i>Add New User
        </a>
    </div>

    <div class="user-table-card table-responsive" data-aos="fade-up" data-aos-duration="1200">
        <table class="table table-users table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                    <tr data-aos="fade-right" data-aos-delay="50" data-aos-duration="1200">
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td class="user-actions">
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success me-2" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>