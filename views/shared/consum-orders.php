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
    <link href="<?= $basePath ?>/assets/css/shared/consum_order.css?v=<?= time() ?>" rel="stylesheet">
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
                        <!-- Orders Grid -->
                        <div class="orders-grid">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <div class="order-id">Kode Order: <?= htmlspecialchars($order['order_code'] ?? 'ORD-XXXX') ?></div>
                                            <div class="order-date"><?= htmlspecialchars($order['created_at']) ?></div>
                                        </div>
                                        <?php
                                        $statusClass = 'status-pending'; // default status
                                        $statusText  = $order['status'];

                                        if ($order['status'] === 'Ready') {
                                            $statusClass = 'status-ready';
                                        } elseif ($order['status'] === 'Dikirim') {
                                            $statusClass = 'status-shipping';
                                        } elseif ($order['status'] === 'Selesai') {
                                            $statusClass = 'status-completed';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= htmlspecialchars($statusText) ?>
                                        </span>
                                    </div>

                                    <div class="order-content">
                                        <?php if (!empty($order['product_image'])): ?>
                                            <div class="order-image-wrapper">
                                                <img src="<?= $basePath . htmlspecialchars($order['product_image']) ?>"
                                                    alt="Foto Produk" class="order-image">
                                            </div>
                                        <?php endif; ?>

                                        <div class="order-row">
                                            <span class="order-label">Nama Customer:</span>
                                            <span class="order-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                                        </div>
                                        <div class="order-row">
                                            <span class="order-label">Line:</span>
                                            <span class="order-value"><?= htmlspecialchars($order['line']) ?></span>
                                        </div>
                                        <div class="order-row">
                                            <span class="order-label">Nama Produk:</span>
                                            <span class="order-value"><?= htmlspecialchars($order['product_name']) ?></span>
                                        </div>
                                        <div class="order-row">
                                            <span class="order-label">Qty:</span>
                                            <span class="order-value"><?= (int)$order['quantity'] ?></span>
                                        </div>
                                        <div class="order-row">
                                            <span class="order-label">Total:</span>
                                            <span class="order-value">Rp <?= number_format($order['price'] * $order['quantity'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>

                                    <button class="detail-button" onclick="showDetail(<?= htmlspecialchars(json_encode($order)) ?>)">
                                        Lihat Rincian Pesanan
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal-overlay" id="detailModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeDetail()">×</button>
            <h2 class="modal-title">Rincian Pesanan</h2>

            <div class="detail-section">
                <div class="detail-section-title">Informasi Customer</div>
                <div class="detail-row">
                    <span class="detail-label">Nama:</span>
                    <span class="detail-value" id="detail-customer"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value" id="detail-department"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Line:</span>
                    <span class="detail-value" id="detail-line"></span>
                </div>
            </div>

            <div class="detail-section">
                <div class="detail-section-title">Informasi Produk</div>
                <div class="detail-row">
                    <span class="detail-label">Item Code:</span>
                    <span class="detail-value" id="detail-itemcode"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nama Produk:</span>
                    <span class="detail-value" id="detail-product"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Product Type:</span>
                    <span class="detail-value" id="detail-type"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Section:</span>
                    <span class="detail-value" id="detail-section"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Drawing:</span>
                    <span class="detail-value" id="detail-drawing"></span>
                </div>
            </div>

            <div class="detail-section">
                <div class="detail-section-title">Detail Pesanan</div>
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value" id="detail-qty"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Harga Satuan:</span>
                    <span class="detail-value" id="detail-price"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Harga:</span>
                    <span class="detail-value" id="detail-total"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" id="detail-status"></span>
                </div>
            </div>

            <?php if ($currentRole === 'admin'): ?>
                <div class="modal-actions">
                    <a href="#" class="btn-action btn-send" id="btn-send">
                        <i class="fas fa-shipping-fast"></i> Kirim Pesanan
                    </a>
                    <a href="#" class="btn-action btn-complete" id="btn-complete">
                        <i class="fas fa-check-circle"></i> Selesai
                    </a>
                    <a href="#" class="btn-action btn-delete" id="btn-delete" onclick="return confirm('Hapus pesanan ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <script>
        const basePath = '<?= $basePath ?>';

        function showDetail(order) {
            document.getElementById('detail-customer').textContent = order.customer_name || '-';
            document.getElementById('detail-department').textContent = order.department || '-';
            document.getElementById('detail-line').textContent = order.line || '-';
            document.getElementById('detail-itemcode').textContent = order.item_code || '-';
            document.getElementById('detail-product').textContent = order.product_name;
            document.getElementById('detail-type').textContent = order.product_type || '-';
            document.getElementById('detail-section').textContent = order.section_name || '-';
            document.getElementById('detail-drawing').innerHTML = order.drawing_file ?
                `<a href="${basePath}${order.drawing_file}" target="_blank">Lihat Drawing</a>` :
                'Tidak ada';
            document.getElementById('detail-qty').textContent = order.quantity;
            document.getElementById('detail-price').textContent = 'Rp ' + parseInt(order.price).toLocaleString('id-ID');
            document.getElementById('detail-total').textContent = 'Rp ' + (parseInt(order.price) * parseInt(order.quantity)).toLocaleString('id-ID');
            document.getElementById('detail-status').textContent = order.status;

            <?php if ($currentRole === 'admin'): ?>
                document.getElementById('btn-send').href = basePath + '/admin/consumable/orders/send/' + order.id;
                document.getElementById('btn-complete').href = basePath + '/admin/consumable/orders/complete/' + order.id;
                document.getElementById('btn-delete').href = basePath + '/admin/consumable/orders/delete/' + order.id;
            <?php endif; ?>

            document.getElementById('detailModal').classList.add('active');
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetail();
            }
        });
    </script>
</body>

</html>