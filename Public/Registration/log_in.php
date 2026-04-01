<?php
include '../../includes/config.php';
include '../../includes/functions.php';
include '../includes/header_2.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;

                if ($role === 'admin') {
                    header("Location: ../../admin/dashboard.php"); 
                } else {
                    header("Location: ../../user/index.php");   
                }
                exit();
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "No account found with this email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Washly</title>
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
                <h2>Login to Your Account</h2>
                <p>Welcome back! Let's get your car washed.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-message" data-aos="fade-left">
                    <?php foreach ($errors as $err): ?>
                        <p><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($err); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="success-message" data-aos="fade-right">
                    <p><i class="bi bi-check-circle-fill"></i> Registration successful! You can now log in.</p>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group input-icon" data-aos="fade-right" data-aos-delay="100">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>
                <div class="form-group input-icon" data-aos="fade-right" data-aos-delay="200">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                
                <button type="submit" class="btn btn-primary-custom" data-aos="zoom-in" data-aos-delay="300">Login</button>
            </form>

            <div class="link-text" data-aos="fade-up">
                Don't have an account? <a href="sign_up.php">Sign Up</a>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="../assets/js/header_script.js"></script>
</body>
</html>
