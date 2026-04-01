document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.service-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const addOnId = this.dataset.id;
            const isChecked = this.checked;
            const newStatus = isChecked ? 1 : 0;

            fetch('process_add_on.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${addOnId}&is_active=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusBadge = this.closest('tr').querySelector('.service-status');
                    if (statusBadge) {
                        statusBadge.classList.toggle('bg-success', isChecked);
                        statusBadge.classList.toggle('bg-danger', !isChecked);
                        statusBadge.textContent = isChecked ? 'Active' : 'Inactive';
                    }
                } else {
                    alert('Failed to update add-on status: ' + data.message);
                    this.checked = !isChecked;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                this.checked = !isChecked;
            });
        });
    });
});