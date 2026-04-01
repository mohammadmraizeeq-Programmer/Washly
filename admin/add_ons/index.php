<?php

if (!is_admin()) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}


// Fetch all add-on services from the database
$stmt = $conn->prepare("SELECT * FROM add_on_services ORDER BY created_at ASC");
$add_ons = [];
if ($stmt) {
    $stmt->execute();
    $add_ons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Add-Ons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/services_style.css">
</head>
<body>

<div id="status-message-container" class="container mt-4"></div>

<section class="add-ons-section py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="display-5 fw-bold mb-2">Manage Add-On Services</h3>
            <button class="btn btn-primary add-new-btn" data-bs-toggle="modal" data-bs-target="#editAddonModal" data-mode="add">
                <i class="bi bi-plus-lg me-1"></i> Add New Add-On
            </button>
        </div>

        <div class="add-ons-grid row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if (!empty($add_ons)): ?>
                <?php foreach ($add_ons as $addon): ?>
                    <div class="col" data-aos="fade-up">
                        <div class="add-on-card h-100 position-relative <?php echo ($addon['is_active'] == 0) ? 'disabled-card' : ''; ?>">
                            <h5 class="add-on-name"><?php echo htmlspecialchars($addon['name']); ?></h5>
                            <p class="add-on-description"><?php echo htmlspecialchars($addon['description']); ?></p>
                            <div class="add-on-meta">
                                <span class="add-on-price">$<?php echo htmlspecialchars($addon['price']); ?></span>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-sm btn-outline-primary edit-addon-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editAddonModal" 
                                    data-mode="edit" 
                                    data-id="<?php echo htmlspecialchars($addon['id']); ?>"
                                    data-name="<?php echo htmlspecialchars($addon['name']); ?>"
                                    data-description="<?php echo htmlspecialchars($addon['description']); ?>"
                                    data-price="<?php echo htmlspecialchars($addon['price']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <?php if ($addon['is_active'] == 1): ?>
                                    <button class="btn btn-sm btn-outline-warning toggle-status-btn" data-id="<?php echo htmlspecialchars($addon['id']); ?>" data-table="add_on_services" data-status="0">
                                        <i class="bi bi-x-circle"></i> Disable
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success toggle-status-btn" data-id="<?php echo htmlspecialchars($addon['id']); ?>" data-table="add_on_services" data-status="1">
                                        <i class="bi bi-check-circle"></i> Enable
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?php echo htmlspecialchars($addon['id']); ?>" data-table="add_on_services">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center col-12">No add-on services found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'edit_addon_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="../assets/js/nav_scroll.js"></script>
<script src="../assets/js/manage_services.js"></script>
</body>
</html>