// admin/assets/js/main_admin.js

// Function to display a temporary alert
function showMessage(message, type = 'success') {
    const container = document.getElementById('status-message-container');
    if (!container) return; // Exit if container doesn't exist

    // Create the alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Clear any existing messages
    container.innerHTML = '';
    
    // Append the new message
    container.appendChild(alertDiv);

    // Automatically remove the alert after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 5000); // 5000 milliseconds = 5 seconds
}


document.addEventListener('DOMContentLoaded', () => {

    AOS.init({
        duration: 1000
    });

    const serviceToggles = document.querySelectorAll('.service-toggle');
    serviceToggles.forEach(toggle => {
        toggle.addEventListener('change', (event) => {
            const id = event.target.dataset.id;
            const table = event.target.dataset.table;
            const is_active = event.target.checked ? 1 : 0;
            const statusBadge = event.target.closest('tr').querySelector('.service-status');
            
            let processPath;
            if (table === 'services') {
                processPath = `/Training/Washly/admin/services/process_service.php`;
            } else if (table === 'add_on_services') {
                processPath = `/Training/Washly/admin/add_ons/process_add_on.php`;
            } else {
                console.error("Unknown table:", table);
                showMessage('An error occurred. Please try again.', 'danger');
                event.target.checked = !is_active;
                return;
            }

            fetch(processPath, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&is_active=${is_active}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (statusBadge) {
                        statusBadge.textContent = (is_active === 1) ? 'Active' : 'Inactive';
                        statusBadge.classList.remove('bg-success', 'bg-danger');
                        statusBadge.classList.add((is_active === 1) ? 'bg-success' : 'bg-danger');
                    }
                    showMessage(data.message); // Display the success message
                } else {
                    event.target.checked = !is_active;
                    showMessage(data.message || 'An error occurred.', 'danger'); // Display the error message
                    console.error('Error:', data.message);
                }
            })
            .catch(err => {
                event.target.checked = !is_active;
                showMessage('An error occurred. Please try again.', 'danger');
                console.error('Network error:', err);
            });
        });
    });
});