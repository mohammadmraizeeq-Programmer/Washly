<nav class="navbar navbar-expand-lg navbar-light nav-compact navbar-transparent" id="adminNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/Training/Washly/admin/dashboard.php">
            <img src="/Training/Washly/assets/images/Washly_Logo.png" alt="Logo" class="me-2" style="height: 40px;">
            <span class="fs-4 fw-bold">Washly</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/Training/Washly/admin/dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Training/Washly/admin/services/index.php">
                        <i class="fas fa-box-open me-2"></i>Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Training/Washly/admin/booking/manage_booking.php">
                        <i class="fas fa-calendar-alt me-2"></i>Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Training/Washly/admin/messages/messages.php">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Training/Washly/admin/users/index.php">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Training/Washly/index.php">
                        <i class="fas fa-sign-out-alt fa-flip-horizontal me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the current page's URL pathname
        const currentPath = window.location.pathname;

        // Find all nav-links
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        // Loop through each link to find a match
        navLinks.forEach(link => {
            if (link.href.includes(currentPath)) {
                // Add the active-link class to the matching link
                link.classList.add('active-link');
                // Optional: set aria-current for accessibility
                link.setAttribute('aria-current', 'page');
            }
        });
    });
</script>