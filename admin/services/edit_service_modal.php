<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="serviceForm" action="process_service.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="service-id" name="id">
                    <input type="hidden" id="form-action" name="action">
                    
                    <div class="mb-3">
                        <label for="service-name" class="form-label">Service Name *</label>
                        <input type="text" class="form-control" id="service-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="service-description" class="form-label">Description *</label>
                        <textarea class="form-control" id="service-description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="service-features" class="form-label">Features (Comma-separated)</label>
                        <input type="text" class="form-control" id="service-features" name="features" placeholder="e.g., Quick wash, Interior vacuum, Tire shine">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service-price" class="form-label">Price ($) *</label>
                            <input type="number" step="0.01" class="form-control" id="service-price" name="price" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="service-duration" class="form-label">Duration *</label>
                            <input type="number" class="form-control" id="service-duration" name="duration" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service-emoji" class="form-label">Emoji</label>
                        <input type="text" class="form-control" id="service-emoji" name="emoji">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveServiceBtn">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</div>