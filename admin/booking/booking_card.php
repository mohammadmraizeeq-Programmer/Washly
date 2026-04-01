<?php
if (!isset($booking)) {
    echo "Error: Booking data not available.";
    exit();
}
?>
<link rel="stylesheet" href="/Training/Washly/admin/assets/css/booking_card.css">
<div class="card booking-card mb-3" data-aos="fade-up">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start">
        <div class="d-flex align-items-start mb-3 mb-md-0 me-md-4">
            <div class="car-icon-container me-4">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div class="booking-details-content flex-grow-1">
                <div class="d-flex align-items-center mb-1">
                    <h5 class="mb-0 fw-bold user-name"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($booking['user_name']) ?></h5>
                    <span class="status-badge ms-2 <?= strtolower(str_replace(' ', '-', $booking['status'])) ?>">
                        <?= htmlspecialchars($booking['status']) ?>
                    </span>
                </div>
                
                <p class="service-name text-muted mb-0"><i class="bi bi-wrench me-1"></i><?= htmlspecialchars($booking['service_name']) ?></p>

                <div class="d-flex flex-wrap booking-info-row mt-3 mb-2">
                    <span class="me-4"><i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars(date('Y-m-d', strtotime($booking['booking_date']))) ?></span>
                    <span class="me-4"><i class="bi bi-clock me-1"></i><?= htmlspecialchars(date('h:i A', strtotime($booking['booking_date']))) ?></span>
                    <span class="me-4"><i class="bi bi-car-front-fill me-1"></i><?= htmlspecialchars($booking['make']) ?> <?= htmlspecialchars($booking['model']) ?></span>
                    <span class="me-4"><i class="bi bi-tag me-1"></i>License: <?= htmlspecialchars($booking['license_plate']) ?></span>
                </div>

                <?php if (!empty($booking['special_requests'])): ?>
                    <p class="special-request-note mt-3 mb-0">Note: <?= htmlspecialchars($booking['special_requests']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="booking-actions d-flex flex-column align-items-end ms-md-auto">
            <h5 class="mb-2 price-text fw-bold text-success">$<?= htmlspecialchars($booking['total_price']) ?></h5>
            <div class="d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-sm view-details-btn me-2" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal" data-id="<?= $booking['id'] ?>">
                    <i class="bi bi-list-ul me-1"></i>View Details
                </button>
                
                <div class="dropdown status-action-dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill me-1"></i><?= htmlspecialchars($booking['status']) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item status-option" href="#" data-status="Pending" data-id="<?= $booking['id'] ?>">Pending</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="Confirmed" data-id="<?= $booking['id'] ?>">Confirmed</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="In-Progress" data-id="<?= $booking['id'] ?>">In Progress</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="Completed" data-id="<?= $booking['id'] ?>">Completed</a></li>
                        <li><a class="dropdown-item status-option" href="#" data-status="Cancelled" data-id="<?= $booking['id'] ?>">Cancelled</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-danger ms-2 delete-booking-btn" data-id="<?= $booking['id'] ?>">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>