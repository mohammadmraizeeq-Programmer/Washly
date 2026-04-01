<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="/Training/Washly/user/assets/css/nav_style.css">

<nav class="navbar navbar-expand-lg" data-aos="fade-down">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <i class="bi bi-droplet me-2"></i> Washly
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php if ($current_page == 'index.php') echo 'active'; ?>" href="/Training/Washly/user/index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($current_page == 'book-service.php') echo 'active'; ?>" href="/Training/Washly/user/booking/book-service.php">
                        <i class="bi bi-calendar-check"></i> Book Service
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($current_page == 'messages.php') echo 'active'; ?>" href="/Training/Washly/user/messages/messages.php">
                        <i class="bi bi-chat-dots"></i> Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($current_page == 'logout.php') echo 'active'; ?>" href="/Training/Washly/index.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800
    });
</script>