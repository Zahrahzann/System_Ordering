<?php
if (!isset($pendingOrders)) {
    die('Controller tidak menyediakan data.');
}
$basePath = '/system_ordering/public';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/spv/work_order/approval.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../../views/layout/topbar.php'; ?>
                <div class="container-fluid">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-clipboard-list"></i>
                            Daftar Work Order Menunggu Approval
                        </h1>
                    </div>

                    <!-- Stats Bar -->
                    <div class="stats-bar">
                        <div class="stats-info">
                            <h3>Total WO Waiting Approval</h3>
                            <p class="count"><?= count($pendingOrders) ?></p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>

                    <?php if (empty($pendingOrders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h4>Semua Work Order Sudah Diproses</h4>
                            <p>Tidak ada work order yang menunggu persetujuan Anda saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingOrders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-id-section">
                                        <div class="order-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="order-id-info">
                                            <div class="order-id-label">Order ID</div>
                                            <h5>#<?= htmlspecialchars($order['order_id']) ?></h5>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pending-badge">
                                            <i class="fas fa-clock"></i>
                                            Menunggu Approval
                                        </span>
                                    </div>
                                </div>

                                <div class="order-details">
                                    <div class="detail-item">
                                        <div class="detail-label">Customer</div>
                                        <div class="detail-value">
                                            <i class="fas fa-user"></i>
                                            <?= htmlspecialchars($order['customer_name']) ?>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Departemen</div>
                                        <div class="detail-value">
                                            <i class="fas fa-building"></i>
                                            <?= htmlspecialchars($order['department_name']) ?>
                                        </div>
                                    </div>                           
                                    <div class="detail-item">
                                        <div class="detail-label">Tanggal Order</div>
                                        <div class="detail-value">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-action">
                                    <a href="<?= $basePath ?>/spv/work_order/detail/<?php echo $order['order_id']; ?>" class="btn btn-info">
                                        <i class="fas fa-search"></i>
                                        Lihat Detail & Approve
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php include __DIR__ . '/../../../views/layout/footer.php'; ?>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>