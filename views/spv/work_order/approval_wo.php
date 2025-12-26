<?php
$basePath = '/system_ordering/public';

// Gunakan fallback jika controller kirim variabel dengan nama berbeda
$orders = $pendingOrders ?? $orders ?? [];

if (!is_array($orders)) {
    die('Controller tidak menyediakan data.');
}

// Ambil flash notification dari session
$notif = null;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['flash_notification'])) {
    $notif = $_SESSION['flash_notification'];
    unset($_SESSION['flash_notification']);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/spv/work_order/approval.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../../views/layout/topbar.php'; ?>
                <div class="container-fluid">
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">Approval Work Order</h1>
                        <p class="page-subtitle">
                            Pantau dan kelola Pengajuan Approval work order yang masuk dari customer
                        </p>
                    </div>

                    <!-- Stats Bar -->
                    <div class="stats-bar">
                        <div class="stats-info">
                            <h3>Total WO (Waiting)</h3>
                            <?php
                            $waitingCount = count(array_filter($orders, fn($o) => $o['approval_status'] === 'waiting'));
                            ?>
                            <p class="count"><?= $waitingCount ?></p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h4>Semua Work Order Sudah Diproses</h4>
                            <p>Tidak ada work order yang menunggu persetujuan atau ditolak saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order):
                            $status = $order['approval_status'] ?? 'waiting';
                            $badgeClass = 'badge-warning';
                            $badgeText  = 'Menunggu Approval';
                            $badgeIcon  = 'fa-clock';

                            if ($status === 'reject') {
                                $badgeClass = 'badge-danger';
                                $badgeText  = 'Ditolak SPV';
                                $badgeIcon  = 'fa-times-circle';
                            }
                        ?>
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
                                        <span class="badge <?= $badgeClass ?>">
                                            <i class="fas <?= $badgeIcon ?>"></i>
                                            <?= $badgeText ?>
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
                                        <div class="detail-label">Line</div>
                                        <div class="detail-value">
                                            <i class="fas fa-layer-group"></i>
                                            <?= htmlspecialchars($order['line']) ?>
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
                                    <a href="<?= $basePath ?>/spv/work_order/detail/<?= $order['order_id'] ?>" class="btn btn-info">
                                        <i class="fas fa-search"></i>
                                        Lihat Detail & Approve
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <!-- SweetAlert Pop Up -->
    <?php if (isset($_SESSION['flash_notification'])): ?>
        <?php $notif = $_SESSION['flash_notification'];
        unset($_SESSION['flash_notification']); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: '<?= $notif['type'] ?>',
                title: '<?= $notif['title'] ?>',
                text: '<?= $notif['message'] ?>'
            });
        </script>
    <?php endif; ?>

</body>

</html>