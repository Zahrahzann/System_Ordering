<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan Consumable</title>
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
                            <i class="fas fa-history"></i>
                            Riwayat Pesanan Consumable
                        </h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                Semua riwayat pesanan customer
                            <?php elseif ($currentRole === 'spv'): ?>
                                Riwayat pesanan departemen Anda
                            <?php else: ?>
                                Riwayat pesanan Anda
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Search & Filter -->
                    <form method="GET" class="search-filter-form">
                        <?php if ($currentRole === 'admin'): ?>
                            <input type="text" name="q" placeholder="Cari produk / kode item / customer"
                                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <?php else: ?>
                            <input type="text" name="q" placeholder="Cari produk / kode item"
                                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <?php endif; ?>

                        <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                            <select name="department">
                                <option value="">-- Pilih Departemen --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>"
                                        <?= ($filters['department'] == $dept['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <select name="month">
                            <option value="">Semua Bulan</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($_GET['month'] ?? '') == $m ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <select name="year">
                            <option value="">Semua Tahun</option>
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= ($_GET['year'] ?? '') == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    </form>

                    <!-- Cards -->
                    <div class="orders-grid">
                        <?php if (empty($orders)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open fa-3x"></i>
                                <h4>Tidak ada riwayat pesanan</h4>
                            </div>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <div class="order-id">Kode Order: <?= htmlspecialchars($order['order_code']) ?></div>
                                            <div class="order-date"><?= htmlspecialchars($order['created_at']) ?></div>
                                        </div>
                                        <span class="status-badge status-complete">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </span>
                                    </div>

                                    <div class="order-content">
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
                                        <?php if ($currentRole !== 'customer'): ?>
                                            <div class="order-row">
                                                <span class="order-label">Customer:</span>
                                                <span class="order-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                                            </div>
                                            <div class="order-row">
                                                <span class="order-label">Departemen:</span>
                                                <span class="order-value"><?= htmlspecialchars($order['department']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <button class="detail-button" onclick="showDetail(<?= htmlspecialchars(json_encode($order)) ?>)">
                                        Lihat Rincian
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal-overlay" id="detailModal">
                        <div class="modal-content">
                            <button class="modal-close" onclick="closeDetail()">×</button>
                            <h2 class="modal-title">Rincian Pesanan</h2>
                            <div id="detailContent"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const basePath = '<?= $basePath ?>';

        function showDetail(order) {
            let html = `
            <p><strong>Kode Order:</strong> ${order.order_code}</p>
            <p><strong>Tanggal:</strong> ${order.created_at}</p>
            <p><strong>Produk:</strong> ${order.product_name}</p>
            <p><strong>Item Code:</strong> ${order.item_code}</p>
            <p><strong>Section:</strong> ${order.section_name}</p>
            <p><strong>Type:</strong> ${order.product_type_name}</p>
            <p><strong>Qty:</strong> ${order.quantity}</p>
            <p><strong>Total:</strong> Rp ${new Intl.NumberFormat('id-ID').format(order.price * order.quantity)}</p>
        `;
            if (order.customer_name) {
                html += `<p><strong>Customer:</strong> ${order.customer_name}</p>`;
            }
            if (order.department) {
                html += `<p><strong>Departemen:</strong> ${order.department}</p>`;
            }
            if (order.line) {
                html += `<p><strong>Line:</strong> ${order.line}</p>`;
            }
            document.getElementById('detailContent').innerHTML = html;
            document.getElementById('detailModal').classList.add('active');
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.remove('active');
        }

        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetail();
            }
        });
    </script>

</body>

</html>