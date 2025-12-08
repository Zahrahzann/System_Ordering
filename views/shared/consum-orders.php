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
    <link href="<?= $basePath ?>/assets/css/shared/consum_order.css" <?= time() ?> rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-clipboard-list"></i>
                            Tracking Pesanan Consumable
                        </h1>
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

                    <!-- Content -->
                    <?php if (empty($orders)): ?>
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="fas fa-box-open fa-3x"></i>
                            <h4>Belum ada pesanan</h4>
                            <p>Silakan checkout dari keranjang untuk membuat pesanan baru.</p>
                            <a href="<?= $basePath ?>/shared/consumable/product-items/" class="btn-primary">
                                Lihat Katalog
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Orders Table -->
                        <div class="orders-container">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>ID Order</th>
                                        <th>Produk</th>
                                        <th>Product Type</th>
                                        <th>Section</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                                            <th>Customer</th>
                                        <?php endif; ?>
                                        <th>Line</th>
                                        <?php if ($currentRole === 'admin'): ?>
                                            <th>Department</th>
                                            <th>Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?= (int)$order['id'] ?></td>
                                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                                            <td><?= htmlspecialchars($order['product_type'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($order['section_name'] ?? '-') ?></td>
                                            <td><?= (int)$order['quantity'] ?></td>
                                            <td>Rp <?= number_format($order['price'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($order['price'] * $order['quantity'], 0, ',', '.') ?></td>
                                            <td><?= htmlspecialchars($order['status']) ?></td>
                                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                                            <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                                                <td><?= htmlspecialchars($order['customer_name'] ?? '-') ?></td>
                                            <?php endif; ?>
                                            <td><?= htmlspecialchars($order['line'] ?? '-') ?></td>
                                            <?php if ($currentRole === 'admin'): ?>
                                                <td><?= htmlspecialchars($order['department']) ?></td>
                                                <td>
                                                    <a href="<?= $basePath ?>/admin/orders/detail/<?= (int)$order['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                                    <a href="<?= $basePath ?>/admin/orders/update_status/<?= (int)$order['id'] ?>" class="btn btn-sm btn-warning">Update</a>
                                                    <a href="<?= $basePath ?>/admin/orders/delete/<?= (int)$order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesanan ini?')">Hapus</a>
                                                </td>
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