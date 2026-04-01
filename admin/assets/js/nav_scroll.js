// admin/assets/js/nav_scroll.js

document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('adminNavbar');
    const scrollThreshold = 50; // The number of pixels to scroll before the effect activates

    window.addEventListener('scroll', function() {
        if (window.scrollY > scrollThreshold) {
            navbar.classList.add('navbar-glass');
            navbar.classList.remove('navbar-transparent');
        } else {
            navbar.classList.remove('navbar-glass');
            navbar.classList.add('navbar-transparent');
        }
    });
});