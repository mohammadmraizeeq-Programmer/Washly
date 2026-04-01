document.addEventListener('DOMContentLoaded', function() {
    // Check if the required elements exist to prevent errors on other pages
    const serviceSelect = document.getElementById('service_id');
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
    const totalPriceEl = document.getElementById('totalPrice');
    const editPageStatusDropdown = document.querySelector('.status-action-dropdown');

    if (serviceSelect && totalPriceEl) {
        // Function to calculate total price
        function calculateTotal() {
            let total = parseFloat(serviceSelect.selectedOptions[0].dataset.price) || 0;
            addonCheckboxes.forEach(cb => {
                if (cb.checked) total += parseFloat(cb.dataset.price);
            });
            totalPriceEl.textContent = total.toFixed(2);
        }

        // Event listeners for price calculation
        serviceSelect.addEventListener('change', calculateTotal);
        addonCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));

        // Initial calculation on page load
        calculateTotal();
    }

    // Logic to handle status change on edit_booking.php
    if (editPageStatusDropdown) {
        editPageStatusDropdown.addEventListener('click', (e) => {
            const statusOption = e.target.closest('.dropdown-item');
            if (statusOption && statusOption.classList.contains('status-option')) {
                e.preventDefault();
                const bookingId = statusOption.dataset.id;
                const newStatus = statusOption.dataset.status;

                const dropdownButton = statusOption.closest('.dropdown').querySelector('.dropdown-toggle');
                
                if (dropdownButton) {
                    dropdownButton.textContent = 'Updating...';
                    dropdownButton.disabled = true;
                }

                // Update the hidden form field for submission
                const statusInput = document.querySelector('input[name="status"]');
                if (statusInput) {
                    statusInput.value = newStatus;
                }

                fetch('./update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${bookingId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (dropdownButton) {
                        dropdownButton.disabled = false;
                        dropdownButton.textContent = newStatus;
                    }

                    if (data.success) {
                        console.log('Status updated successfully:', data.message);
                        const statusBadge = document.querySelector('.status-badge');
                        if (statusBadge) {
                            statusBadge.textContent = newStatus;
                            statusBadge.className = `status-badge ${newStatus.toLowerCase().replace(' ', '-')}`;
                        }
                    } else {
                        console.error('Error updating status:', data.message);
                        alert('Error updating status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    alert('An unexpected error occurred. Please check the console.');
                    if (dropdownButton) {
                        dropdownButton.disabled = false;
                    }
                });
            }
        });
    }

    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            once: true
        });
    }
});