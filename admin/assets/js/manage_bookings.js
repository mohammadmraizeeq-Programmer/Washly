document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatusText');
    const bookingsList = document.getElementById('bookingsList');
    
    // Check if the modal element exists before trying to create a modal instance
    const modalElement = document.getElementById('bookingDetailsModal');
    if (modalElement) {
        const bookingDetailsModal = new bootstrap.Modal(modalElement);
        
        // Event listener for all booking card actions using event delegation on manage_booking.php
        bookingsList.addEventListener('click', (e) => {
            // View Details button logic
            const viewDetailsBtn = e.target.closest('.view-details-btn');
            if (viewDetailsBtn) {
                e.preventDefault();
                const bookingId = viewDetailsBtn.dataset.id;
                
                // Fetch booking details via AJAX
                fetch(`get_booking_details.php?id=${bookingId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(booking => {
                        if (booking.error) {
                            alert(booking.error);
                            return;
                        }
                        
                        // Populate the modal with booking data
                        document.getElementById('modalUserName').textContent = booking.user_name;
                        document.getElementById('modalUserEmail').textContent = booking.user_email;
                        document.getElementById('modalUserPhone').textContent = booking.user_phone;

                        // Update the status badge
                        const statusBadge = document.getElementById('modalStatusBadge');
                        statusBadge.textContent = booking.status;
                        statusBadge.className = `status-badge ${booking.status.toLowerCase().replace(' ', '-')}`;

                        document.getElementById('modalServiceName').textContent = booking.service_name;
                        document.getElementById('modalServicePrice').textContent = booking.service_price;
                        document.getElementById('modalTotalFinalPrice').textContent = booking.total_price;
                        
                        const bookingDate = new Date(booking.booking_date);
                        document.getElementById('modalBookingDate').textContent = bookingDate.toLocaleDateString();
                        document.getElementById('modalBookingTime').textContent = bookingDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

                        document.getElementById('modalVehicleInfo').textContent = `${booking.make} ${booking.model} (${booking.car_type})`;
                        document.getElementById('modalLicensePlate').textContent = booking.license_plate;
                        document.getElementById('modalSpecialNotes').textContent = booking.special_requests || 'N/A';
                        
                        // Set the edit button URL
                        document.getElementById('modalEditButton').href = `edit_booking.php?id=${booking.id}`;

                        bookingDetailsModal.show();
                    })
                    .catch(error => console.error('Error fetching booking details:', error));
            }
        });
    }

    // Function to load bookings via AJAX
    const fetchBookings = (searchTerm = '', statusFilter = 'All Status') => {
        const url = `get_filtered_bookings.php?search=${encodeURIComponent(searchTerm)}&status=${encodeURIComponent(statusFilter)}`;
        fetch(url)
            .then(response => response.text())
            .then(html => {
                bookingsList.innerHTML = html;
                if (typeof AOS !== 'undefined') {
                    AOS.init(); // Re-initialize AOS for new elements
                }
            })
            .catch(error => console.error('Error fetching bookings:', error));
    };

    // Initial load, only on manage_booking.php
    if (bookingsList) {
        fetchBookings();
        searchInput.addEventListener('input', () => fetchBookings(searchInput.value, filterStatus.value));
        filterStatus.addEventListener('change', () => fetchBookings(searchInput.value, filterStatus.value));
    }

    // Event listener for delete and status change actions
    if (bookingsList) {
        bookingsList.addEventListener('click', (e) => {
            // Delete Booking button logic
            const deleteBtn = e.target.closest('.delete-booking-btn');
            if (deleteBtn) {
                e.preventDefault();
                const bookingId = deleteBtn.dataset.id;
                
                if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
                    fetch('delete_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `booking_id=${bookingId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Booking deleted successfully!');
                            fetchBookings(searchInput.value, filterStatus.value); // Refresh the list
                        } else {
                            alert('Error deleting booking: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
            
            // Status dropdown change logic
            const statusOption = e.target.closest('.status-option');
            if (statusOption) {
                e.preventDefault();
                const bookingId = statusOption.dataset.id;
                const newStatus = statusOption.dataset.status;

                const dropdownButton = statusOption.closest('.dropdown').querySelector('.dropdown-toggle');
                
                // Show loading state and disable button
                if (dropdownButton) {
                    dropdownButton.textContent = 'Updating...';
                    dropdownButton.disabled = true;
                }

                // Correcting the fetch URL and implementing the update logic
                fetch('../booking/update_booking_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${bookingId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    // Re-enable the button
                    if (dropdownButton) {
                        dropdownButton.disabled = false;
                        dropdownButton.textContent = newStatus;
                    }

                    if (data.success) {
                        console.log('Status updated successfully:', data.message);
                        // Find the card and update the status badge
                        const card = document.querySelector(`.booking-card [data-id="${bookingId}"]`).closest('.booking-card');
                        const statusBadge = card.querySelector('.status-badge');
                        
                        // Update the text and class of the badge
                        if (statusBadge) {
                            statusBadge.textContent = newStatus;
                            statusBadge.className = `status-badge ms-2 ${newStatus.toLowerCase().replace(' ', '-')}`;
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
});