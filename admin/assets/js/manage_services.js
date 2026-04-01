document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        duration: 800,
        once: true
    });

    // Helper function to show alerts
    function showAlert(message, type) {
        const alertContainer = document.getElementById('status-message-container');
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        // Auto-hide the alert after 5 seconds
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(alertContainer.querySelector('.alert'));
            alert.close();
        }, 5000);
    }

    // Generic function to handle form submission (Add/Edit)
    function handleFormSubmission(formId, processUrl, modalId) {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch(processUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok. The file path might be incorrect.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modal) {
                        modal.hide();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while processing your request. Check the console for details.', 'danger');
                });
            });
        }
    }

    // Generic function to handle delete and toggle status actions
    function handleAction(selector, processUrl, actionName, confirmationMessage) {
        document.querySelectorAll(selector).forEach(button => {
            button.addEventListener('click', function() {
                if (confirmationMessage && !confirm(confirmationMessage)) {
                    return;
                }

                const id = this.dataset.id;
                const status = this.dataset.status;
                const formData = new FormData();
                
                formData.append('id', id);
                formData.append('action', actionName);
                if (actionName === 'toggle_status') {
                    formData.append('is_active', status);
                }
                
                fetch(processUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                         throw new Error('Network response was not ok. The file path might be incorrect.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while processing your request. Check the console for details.', 'danger');
                });
            });
        });
    }

    // Handle "Edit" and "Add" modal for SERVICES
    const editServiceModal = document.getElementById('editServiceModal');
    if (editServiceModal) {
        editServiceModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const modalTitle = editServiceModal.querySelector('.modal-title');
            const form = editServiceModal.querySelector('#serviceForm');
            const saveBtn = editServiceModal.querySelector('#saveServiceBtn');

            if (button.dataset.mode === 'add') {
                modalTitle.textContent = 'Add New Service';
                saveBtn.textContent = 'Add Service';
                form.reset();
                form.querySelector('#form-action').value = 'add';
                form.querySelector('#service-id').value = '';
            } else if (button.dataset.mode === 'edit') {
                modalTitle.textContent = 'Edit Service';
                saveBtn.textContent = 'Update Service';
                form.querySelector('#form-action').value = 'edit';
                form.querySelector('#service-id').value = button.dataset.id;
                form.querySelector('#service-name').value = button.dataset.name;
                form.querySelector('#service-description').value = button.dataset.description;
                form.querySelector('#service-features').value = button.dataset.features;
                form.querySelector('#service-price').value = button.dataset.price;
                form.querySelector('#service-duration').value = button.dataset.duration;
                form.querySelector('#service-emoji').value = button.dataset.emoji;
            }
        });
    }

    // Handle "Edit" and "Add" modal for ADD-ONS
    const editAddonModal = document.getElementById('editAddonModal');
    if (editAddonModal) {
        editAddonModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const modalTitle = editAddonModal.querySelector('.modal-title');
            const form = editAddonModal.querySelector('#addonForm');
            const saveBtn = editAddonModal.querySelector('#saveAddonBtn');

            if (button.dataset.mode === 'add') {
                modalTitle.textContent = 'Add New Add-On';
                saveBtn.textContent = 'Add Add-On';
                form.reset();
                form.querySelector('#form-action').value = 'add';
                form.querySelector('#addon-id').value = '';
            } else if (button.dataset.mode === 'edit') {
                modalTitle.textContent = 'Edit Add-On';
                saveBtn.textContent = 'Update Add-On';
                form.querySelector('#form-action').value = 'edit';
                form.querySelector('#addon-id').value = button.dataset.id;
                form.querySelector('#addon-name').value = button.dataset.name;
                form.querySelector('#addon-description').value = button.dataset.description;
                form.querySelector('#addon-price').value = button.dataset.price;
            }
        });
    }

    // Attach event listeners using the generic functions
    handleFormSubmission('serviceForm', '../services/process_service.php', 'editServiceModal');
    handleFormSubmission('addonForm', '../add_ons/process_add_on.php', 'editAddonModal');
    handleAction('.delete-btn[data-table="services"]', '../services/process_service.php', 'delete', 'Are you sure you want to delete this service? This action cannot be undone.');
    handleAction('.delete-btn[data-table="add_on_services"]', '../add_ons/process_add_on.php', 'delete', 'Are you sure you want to delete this add-on? This action cannot be undone.');
    handleAction('.toggle-status-btn[data-table="services"]', '../services/process_service.php', 'toggle_status', null);
    handleAction('.toggle-status-btn[data-table="add_on_services"]', '../add_ons/process_add_on.php', 'toggle_status', null);
});