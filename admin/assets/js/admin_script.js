// admin/assets/js/admin_script.js

document.addEventListener('DOMContentLoaded', function() {
    // --- Navbar Scroll Effect ---
    const navbar = document.getElementById('adminNavbar');
    const scrollThreshold = 50;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > scrollThreshold) {
            navbar.classList.add('navbar-glass');
            navbar.classList.remove('navbar-transparent');
        } else {
            navbar.classList.remove('navbar-glass');
            navbar.classList.add('navbar-transparent');
        }
    });

    // --- Active Nav Link Highlight ---
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPagePath = window.location.pathname;

    navLinks.forEach(link => {
        // Check if the link's href matches the current page, or if it's the users page
        // to handle the different pages within the users folder
        if (currentPagePath.includes(link.getAttribute('href'))) {
            link.classList.add('active-link');
        }
    });

    // --- Delete Confirmation ---
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmDelete = confirm("Are you sure you want to delete this user? This action cannot be undone.");
            if (!confirmDelete) {
                event.preventDefault();
            }
        });
    });
});