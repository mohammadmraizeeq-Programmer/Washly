<div class="modal fade" id="editAddonModal" tabindex="-1" aria-labelledby="editAddonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAddonModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addonForm" action="process_addon.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="addon-id" name="id">
                    <input type="hidden" id="form-action" name="action">
                    
                    <div class="mb-3">
                        <label for="addon-name" class="form-label">Add-On Name *</label>
                        <input type="text" class="form-control" id="addon-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="addon-description" class="form-label">Description</label>
                        <textarea class="form-control" id="addon-description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="addon-price" class="form-label">Price ($) *</label>
                        <input type="number" step="0.01" class="form-control" id="addon-price" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveAddonBtn">Update Add-On</button>
                </div>
            </form>
        </div>
    </div>
</div>