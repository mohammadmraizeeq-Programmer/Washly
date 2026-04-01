<?php
include '../../includes/config.php';
include '../../includes/functions.php';
include '../includes/header_2.php';
session_start();

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role for new users

    // Validation checks
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "An account with this email already exists.";
        }
        $stmt->close();

        // If no errors, proceed with registration
        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                // Redirect to login page with a success message
                header("Location: log_in.php?registered=1");
                exit();
            } else {
                $errors[] = "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Washly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link href="../assets/css/reg_style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <main class="content-wrapper">
        <div class="form-container" data-aos="fade-up">
            <div class="form-header">
                <img src="../../assets/images/Washly_Logo.png" alt="Washly Logo">
                <h2>Create an Account</h2>
                <p>Join us and get your car sparkling!</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-message" data-aos="fade-left">
                    <?php foreach ($errors as $err): ?>
                        <p><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($err); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group input-icon" data-aos="fade-right" data-aos-delay="100">
                    <i class="bi bi-person-fill"></i>
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required value="<?= htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>
                <div class="form-group input-icon" data-aos="fade-right" data-aos-delay="200">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group input-icon" data-aos="fade-right" data-aos-delay="300">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-group input-icon" data-aos="fade-right" data-aos-delay="400">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="btn btn-primary-custom" data-aos="zoom-in" data-aos-delay="500">Sign Up</button>
            </form>

            <div class="link-text" data-aos="fade-up" onclick="window.location.href='log_in.php';" style="cursor: pointer;">
                Already have an account? <span style="text-decoration: underline; color: blue;">Log In</span>
            </div>

    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="../assets/js/header_script.js"></script>
</body>

</html>