<?php
session_start();
include '../../includes/functions.php';
include '../../includes/config.php';

if (!is_admin()) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}

include '../includes/admin_nav.php';

// Fetch all services from the database
$stmt = $conn->prepare("SELECT * FROM services ORDER BY created_at ASC");
$services = [];
if ($stmt) {
    $stmt->execute();
    $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    <title>Manage Services & Add-Ons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_nav_style.css">
    <link rel="stylesheet" href="../assets/css/services_style.css">
</head>

<body>

    <div id="status-message-container" class="container mt-4"></div>

    <section class="services-section py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="display-5 fw-bold mb-2">Manage Services</h3>
                <button class="btn btn-primary add-new-btn" data-bs-toggle="modal" data-bs-target="#editServiceModal" data-mode="add">
                    <i class="bi bi-plus-lg me-1"></i> Add New Service
                </button>
            </div>

            <div class="services-grid row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <div class="col" data-aos="fade-up">
                            <div class="service-card h-100 position-relative <?php echo ($service['is_active'] == 0) ? 'disabled-card' : ''; ?>">
                                <div class="service-emoji-wrapper">
                                    <span><?php echo htmlspecialchars($service['emoji']); ?></span>
                                </div>
                                <h5 class="service-name"><?php echo htmlspecialchars($service['name']); ?></h5>
                                <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                                <ul class="service-features-list">
                                    <?php
                                    $features_array = explode(',', $service['features']);
                                    foreach ($features_array as $feature):
                                        $trimmed_feature = trim(htmlspecialchars($feature));
                                        if (!empty($trimmed_feature)): ?>
                                            <li><i class="bi bi-check-circle-fill"></i> <?php echo $trimmed_feature; ?></li>
                                    <?php endif;
                                    endforeach; ?>
                                </ul>
                                <div class="service-meta">
                                    <span class="service-price">$<?php echo htmlspecialchars($service['price']); ?></span>
                                    <span class="service-duration"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($service['duration']); ?> min</span>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-sm btn-outline-primary edit-service-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editServiceModal"
                                        data-mode="edit"
                                        data-id="<?php echo htmlspecialchars($service['id']); ?>"
                                        data-name="<?php echo htmlspecialchars($service['name']); ?>"
                                        data-description="<?php echo htmlspecialchars($service['description']); ?>"
                                        data-features="<?php echo htmlspecialchars($service['features'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-price="<?php echo htmlspecialchars($service['price']); ?>"
                                        data-duration="<?php echo htmlspecialchars($service['duration']); ?>"
                                        data-emoji="<?php echo htmlspecialchars($service['emoji']); ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm toggle-status-btn <?php echo ($service['is_active'] == 1) ? 'btn-outline-warning' : 'btn-outline-success'; ?>" data-id="<?php echo htmlspecialchars($service['id']); ?>" data-table="services" data-status="<?php echo ($service['is_active'] == 1) ? '0' : '1'; ?>">
                                        <?php if ($service['is_active'] == 1): ?>
                                            <i class="bi bi-x-circle"></i> Disable
                                        <?php else: ?>
                                            <i class="bi bi-check-circle"></i> Enable
                                        <?php endif; ?>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?php echo htmlspecialchars($service['id']); ?>" data-table="services">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center col-12">No services found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="add-ons-section" class="add-ons-section py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="display-5 fw-bold mb-2">Manage Add-On Services</h3>
                <button class="btn btn-primary add-new-btn" data-bs-toggle="modal" data-bs-target="#editAddonModal" data-mode="add">
                    <i class="bi bi-plus-lg me-1"></i> Add New Add-On
                </button>
            </div>

            <div class="add-ons-list row row-cols-1 g-4">
                <?php if (!empty($add_ons)): ?>
                    <?php foreach ($add_ons as $addon): ?>
                        <div class="col" data-aos="fade-up">
                            <div class="add-on-item d-flex justify-content-between align-items-center <?php echo ($addon['is_active'] == 0) ? 'disabled-item' : ''; ?>">
                                <div class="add-on-info d-flex align-items-center">
                                    <div class="add-on-icon me-3">
                                        <i class="bi bi-plus-circle-fill"></i>
                                    </div>
                                    <div class="add-on-text">
                                        <h5 class="add-on-name m-0"><?php echo htmlspecialchars($addon['name']); ?></h5>
                                        <p class="add-on-description m-0 text-muted"><?php echo htmlspecialchars($addon['description']); ?></p>
                                    </div>
                                </div>
                                <div class="add-on-actions d-flex align-items-center gap-3">
                                    <span class="add-on-price fw-bold">$<?php echo htmlspecialchars($addon['price']); ?></span>
                                    <button class="btn btn-sm btn-outline-primary edit-addon-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editAddonModal" 
                                        data-mode="edit" 
                                        data-id="<?php echo htmlspecialchars($addon['id']); ?>"
                                        data-name="<?php echo htmlspecialchars($addon['name']); ?>"
                                        data-description="<?php echo htmlspecialchars($addon['description']); ?>"
                                        data-price="<?php echo htmlspecialchars($addon['price']); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm toggle-status-btn <?php echo ($addon['is_active'] == 1) ? 'btn-outline-warning' : 'btn-outline-success'; ?>" data-id="<?php echo htmlspecialchars($addon['id']); ?>" data-table="add_on_services" data-status="<?php echo ($addon['is_active'] == 1) ? '0' : '1'; ?>">
                                        <?php if ($addon['is_active'] == 1): ?>
                                            <i class="bi bi-x-circle"></i>
                                        <?php else: ?>
                                            <i class="bi bi-check-circle"></i>
                                        <?php endif; ?>
                                    </button>
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

    <?php include 'edit_service_modal.php'; ?>
    <?php include '../add_ons/edit_addon_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="../assets/js/nav_scroll.js"></script>
    <script src="../assets/js/manage_services.js"></script>
</body>

</html>