<?php
$allPendingOrders = $latestOrderItems; // Ambil data dari controller
$basePath = '/system_ordering/public';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Approval Pesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/customer/work_order/checkout.css?v=<?= time() ?>" rel="stylesheet">

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
                            <i class="fas fa-clipboard-check"></i>
                            Status Approval Pesanan
                        </h1>
                    </div>

                    <!-- Notifikasi sukses (saat baru checkout) -->
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?= htmlspecialchars($_SESSION['flash_message']) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['flash_message']); ?>
                    <?php endif; ?>

                    <!-- KOREKSI: Notifikasi (jika ada) dari aksi delete -->
                    <?php if (isset($_SESSION['flash_notification'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_notification']['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?= $_SESSION['flash_notification']['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i>
                            <?= htmlspecialchars($_SESSION['flash_notification']['message']) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['flash_notification']); ?>
                    <?php endif; ?>

                    <?php if (empty($allPendingOrders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h4>Semua Pesanan Sudah Diproses</h4>
                            <p>Tidak ada pesanan yang sedang menunggu persetujuan atau ditolak saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($allPendingOrders as $orderData):
                            $status = $orderData['order_details']['approval_status'];
                            $badgeClass = 'badge-warning'; // Default (waiting)
                            $badgeIcon = 'fa-clock';
                            if ($status === 'reject') {
                                $badgeClass = 'badge-danger';
                                $badgeIcon = 'fa-times-circle';
                            }
                        ?>
                            <div class="order-card">
                                <div class="card-body">
                                    <div class="order-header">
                                        <div class="order-info">
                                            <h5>Order #<?= htmlspecialchars($orderData['order_details']['order_id']) ?></h5>
                                            <div class="order-date">
                                                <i class="far fa-calendar"></i>
                                                <?= date('d F Y', strtotime($orderData['order_details']['order_date'])) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <!-- Badge dan Status dinamis -->
                                            <span class="badge <?= $badgeClass ?>">
                                                <i class="fas <?= $badgeIcon ?> mr-1"></i>
                                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php foreach ($orderData['items'] as $item): ?>
                                        <div class="item-row">
                                            <div class="item-image">
                                                <?php
                                                $files = json_decode($item['file_path'] ?? '[]', true);
                                                if (!empty($files) && is_array($files)) {
                                                    $firstFile = $files[0];
                                                    $fileCount = count($files);
                                                    $extension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                        echo '<img src="' . htmlspecialchars($firstFile) . '" alt="' . htmlspecialchars($item['item_name']) . '">';
                                                        if ($fileCount > 1) {
                                                            echo '<div class="image-count-badge">+' . ($fileCount - 1) . '</div>';
                                                        }
                                                    } elseif ($extension === 'pdf') {
                                                        echo '<div class="item-image-placeholder"><i class="fas fa-file-pdf text-danger"></i></div>';
                                                    } else {
                                                        echo '<div class="item-image-placeholder"><i class="fas fa-file-alt"></i></div>';
                                                    }
                                                } else {
                                                    echo '<div class="item-image-placeholder"><i class="fas fa-tools"></i></div>';
                                                }
                                                ?>
                                            </div>
                                            <!-- Detail item -->
                                            <div class="item-details">
                                                <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                                <div class="item-specs">
                                                    <div class="spec-item">
                                                        <span class="spec-label">Kategori:</span>
                                                        <span class="spec-value"><?= htmlspecialchars($item['category']) ?></span>
                                                    </div>
                                                    <div class="spec-item">
                                                        <span class="spec-label">Material:</span>
                                                        <span class="spec-value"><?= htmlspecialchars($item['material']) ?></span>
                                                    </div>
                                                    <div class="spec-item">
                                                        <span class="spec-label">Jenis Material:</span>
                                                        <span class="spec-value"><?= htmlspecialchars($item['material_type']) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="item-meta">
                                                <div class="qty-badge">
                                                    Qty: <?= htmlspecialchars($item['quantity']) ?>
                                                </div>
                                                <div class="status-badge-wrapper">
                                                    <div class="status-label">Status Emergency</div>
                                                    <?php
                                                    if ($item['is_emergency']) {
                                                        echo $item['emergency_type'] === 'line_stop'
                                                            ? '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Line Stop</span>'
                                                            : '<span class="badge badge-info"><i class="fas fa-shield-alt mr-1"></i>Safety</span>';
                                                    } else {
                                                        echo '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Regular</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($approval['comments'])): ?>
                                                <div class="comments-box">
                                                    <div class="comments-label">Catatan SPV</div>
                                                    <div class="comments-text"><?= nl2br(htmlspecialchars($approval['comments'])) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <!-- Akhir detail item -->
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Approval = Reject -->
                                    <?php if ($status === 'reject'): ?>
                                        <div class="order-footer-actions">
                                            <a href="<?= $basePath ?>/customer/order/delete/<?= $orderData['order_details']['order_id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan yang ditolak ini?')">
                                                <i class="fas fa-trash"></i> Hapus Pesanan
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="back-button-wrapper">
                        <a href="<?= $basePath ?>/customer/dashboard" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <?php include __DIR__ . '/../../../views/layout/footer.php'; ?>
        </div>
    </div>
    <!-- ... SCRIPT ... -->
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>