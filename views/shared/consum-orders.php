<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tracking Pesanan Consumable</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/customer/consumable/consum_orders.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Header -->
                    <div class="page-header">
                        <h1 class="page-title"><i class="fas fa-clipboard-list"></i> Tracking Pesanan Consumable</h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                List pesanan customer
                            <?php elseif ($currentRole === 'spv'): ?>
                                Informasi pesanan dari departemen Anda
                            <?php else: ?>
                                Pesanan Anda
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Konten -->
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open fa-3x"></i>
                            <h4>Belum ada pesanan</h4>
                            <p>Silakan checkout dari keranjang untuk membuat pesanan baru.</p>
                            <a href="<?= $basePath ?>/shared/consumable/product-items/" class="btn-primary">Lihat Katalog</a>
                        </div>
                    <?php else: ?>
                        <div class="orders-container">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                                            <th>Customer</th>
                                        <?php endif; ?>
                                        <?php if ($currentRole === 'spv'): ?>
                                            <th>Departemen</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <div class="order-product">
                                                    <strong><?= htmlspecialchars($order['product_name']) ?></strong><br>
                                                    <small>ID Produk: <?= (int)$order['product_item_id'] ?></small>
                                                </div>
                                            </td>
                                            <td><?= (int)$order['quantity'] ?></td>
                                            <td>Rp <?= number_format($order['price'], 0, ',', '.') ?></td>
                                            <td>
                                                <?php if ($order['status'] === 'Ready'): ?>
                                                    <span class="status-label status-ready">
                                                        <i class="fas fa-check-circle"></i> Ready
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-label status-pending">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($order['created_at']) ?></td>

                                            <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                                                <td><?= htmlspecialchars($order['customer_name'] ?? '-') ?></td>
                                            <?php endif; ?>
                                            <?php if ($currentRole === 'admin'): ?>
                                                <td><?= htmlspecialchars($order['department_id'] ?? '-') ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>
</html>
