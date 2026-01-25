<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Consumable</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/shared/consum_history.css?v=<?= time() ?>" rel="stylesheet">
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
                        <h1 class="page-title">Riwayat Pesanan</h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                Pantau status pesanan customer terbaru, kelola pengembalian dengan mudah, dan dapatkan insight berharga
                            <?php elseif ($currentRole === 'spv'): ?>
                                Pantau status pesanan departemen terbaru dan kelola pengembalian dengan mudah
                            <?php else: ?>
                                Pantau status pesanan terbaru Anda, kelola pengembalian dengan mudah, dan dapatkan insight berharga
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form method="GET" class="search-filter-form">
                            <?php if ($currentRole === 'admin'): ?>
                                <input type="text" name="q" placeholder="Cari produk, kode item, atau customer..."
                                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                            <?php else: ?>
                                <input type="text" name="q" placeholder="Cari produk atau kode item..."
                                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                            <?php endif; ?>

                            <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                                <select name="department">
                                    <option value="">Semua Departemen</option>
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

                            <button type="submit" class="btn-filter">
                                <i class="fas fa-search"></i>
                                Cari
                            </button>
                        </form>
                    </div>

                    <!-- Orders List -->
                    <div class="orders-list">
                        <?php if (empty($orders)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open fa-4x"></i>
                                <h4>Belum ada riwayat pesanan</h4>
                                <p>Pesanan yang sudah selesai akan muncul di sini</p>
                            </div>
                        <?php else: ?>
                            <?php
                            // Group orders by order_code
                            $groupedOrders = [];
                            foreach ($orders as $order) {
                                $groupedOrders[$order['order_code']][] = $order;
                            }
                            ?>

                            <?php foreach ($groupedOrders as $orderCode => $orderItems): ?>
                                <?php $firstItem = $orderItems[0]; ?>
                                <div class="order-card">
                                    <!-- Order Header -->
                                    <div class="order-header">
                                        <div class="order-meta">
                                            <span class="order-meta-label">Kode Pesanan</span>
                                            <span class="order-meta-value">#<?= htmlspecialchars($orderCode) ?></span>
                                        </div>
                                        <div class="order-meta">
                                            <span class="order-meta-label">Tanggal Selesai</span>
                                            <span class="order-meta-value"><?= date('d M Y', strtotime($firstItem['created_at'])) ?></span>
                                        </div>
                                        <?php if ($currentRole !== 'customer'): ?>
                                            <div class="order-meta">
                                                <span class="order-meta-label">Nama Customer</span>
                                                <span class="order-meta-value"><?= htmlspecialchars($firstItem['customer_name']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <!-- <div class="order-actions-header">
                                            <button class="btn-view-invoice">Lihat Invoice</button>
                                        </div> -->
                                    </div>

                                    <!-- Order Body -->
                                    <div class="order-body">
                                        <?php foreach ($orderItems as $item): ?>
                                            <div class="order-item-row">
                                                <div class="order-image-container">
                                                    <?php if (!empty($item['product_image'])): ?>
                                                        <img src="<?= $basePath . htmlspecialchars($item['product_image']) ?>"
                                                            alt="<?= htmlspecialchars($item['product_name']) ?>">
                                                    <?php else: ?>
                                                        <i class="fas fa-box no-image"></i>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="order-item-info">
                                                    <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                                    <div class="product-subtitle"><?= htmlspecialchars($item['section_name']) ?></div>
                                                    <div class="product-details">
                                                        Qty: <?= (int)$item['quantity'] ?> |
                                                        Kode Pesanan: #<?= htmlspecialchars($item['item_code']) ?>
                                                    </div>
                                                </div>

                                                <div class="order-item-right">
                                                    <div class="product-price">
                                                        Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                                                    </div>
                                                    <div class="order-item-actions">
                                                        <button class="btn-view-product" onclick="showDetail(<?= htmlspecialchars(json_encode($item)) ?>)">
                                                            Detail Pesanan
                                                        </button>

                                                        <?php if ($currentRole === 'customer' && $item['status'] === 'Selesai'): ?>
                                                            <button class="btn-buy-again" onclick="openReorderModal(<?= htmlspecialchars(json_encode($item)) ?>)">
                                                                Pesan Lagi
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Order Footer -->
                                    <div class="order-footer">
                                        <span class="status-badge status-delivered">
                                            <i class="fas fa-check-circle"></i>
                                            Dikirim Pada <?= date('d M Y', strtotime($firstItem['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal-overlay" id="detailModal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Detail Pesanan</h2>
                                <button class="modal-close" onclick="closeDetail()">×</button>
                            </div>
                            <div class="modal-body" id="detailContent"></div>
                        </div>
                    </div>

                    <!-- Modal Pesan Lagi -->
                    <div class="modal-overlay" id="reorderModal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Pesan Lagi</h2>
                                <button class="modal-close" onclick="closeReorderModal()">×</button>
                            </div>
                            <div class="modal-body">
                                <form id="reorderForm" method="POST" action="<?= $basePath ?>/customer/shared/consumable/reorder">
                                    <input type="hidden" name="order_id" id="reorder_order_id">
                                    <input type="hidden" name="product_item_id" id="reorder_product_item_id">
                                    <input type="hidden" name="product_type_id" id="reorder_product_type_id">
                                    <input type="hidden" name="section_id" id="reorder_section_id">
                                    <input type="hidden" name="price" id="reorder_price_value">

                                    <div class="detail-row">
                                        <strong>Produk</strong>
                                        <span id="reorder_product"></span>
                                    </div>

                                    <div class="detail-row">
                                        <strong>Jumlah</strong>
                                        <input type="number" name="quantity" id="reorder_quantity" min="1">
                                    </div>

                                    <div class="detail-row">
                                        <strong>Harga Satuan</strong>
                                        <span id="reorder_price"></span>
                                    </div>

                                    <div class="detail-row">
                                        <strong>Total Harga</strong>
                                        <span id="reorder_total"></span>
                                    </div>

                                    <div class="modal-actions">
                                        <button type="submit" class="btn-confirm">Pesan Sekarang</button>
                                        <button type="button" onclick="closeReorderModal()">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <script>
        const basePath = '<?= $basePath ?>';

        function showDetail(order) {
            let html = `
                <div class="detail-row">
                    <strong>Kode Pesanan</strong>
                    <span>${order.order_code}</span>
                </div>
                <div class="detail-row">
                    <strong>Tanggal</strong>
                    <span>${order.created_at}</span>
                </div>
                <div class="detail-row">
                    <strong>Nama Produk</strong>
                    <span>${order.product_name}</span>
                </div>
                <div class="detail-row">
                    <strong>Kode Item</strong>
                    <span>${order.item_code}</span>
                </div>
                <div class="detail-row">
                    <strong>Section</strong>
                    <span>${order.section_name}</span>
                </div>
                <div class="detail-row">
                    <strong>Tipe</strong>
                    <span>${order.product_type_name}</span>
                </div>
                <div class="detail-row">
                    <strong>Quantity</strong>
                    <span>${order.quantity} pcs</span>
                </div>
                <div class="detail-row">
                    <strong>Harga Satuan</strong>
                    <span>Rp ${new Intl.NumberFormat('id-ID').format(order.price)}</span>
                </div>
                <div class="detail-row">
                    <strong>Total Harga</strong>
                    <span style="font-weight: 700; font-size: 1.1rem;">
                        Rp ${new Intl.NumberFormat('id-ID').format(order.price * order.quantity)}
                    </span>
                </div>
            `;

            if (order.customer_name) {
                html += `
                    <div class="detail-row">
                        <strong>Customer</strong>
                        <span>${order.customer_name}</span>
                    </div>
                `;
            }

            if (order.department) {
                html += `
                    <div class="detail-row">
                        <strong>Departemen</strong>
                        <span>${order.department}</span>
                    </div>
                `;
            }

            if (order.line) {
                html += `
                    <div class="detail-row">
                        <strong>Line</strong>
                        <span>${order.line}</span>
                    </div>
                `;
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

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetail();
            }
        });

        // FITUR "PESAN LAGI"
        let reorderUnitPrice = 0;

        function openReorderModal(order) {
            document.getElementById('reorder_order_id').value = order.id;
            document.getElementById('reorder_product_item_id').value = order.product_item_id;
            document.getElementById('reorder_product_type_id').value = order.product_type_id;
            document.getElementById('reorder_section_id').value = order.section_id;
            document.getElementById('reorder_price_value').value = order.price;

            document.getElementById('reorder_product').innerText = order.product_name;
            document.getElementById('reorder_quantity').value = order.quantity;

            reorderUnitPrice = order.price;
            document.getElementById('reorder_price').innerText =
                "Rp " + new Intl.NumberFormat('id-ID').format(reorderUnitPrice);

            updateReorderTotal();
            document.getElementById('reorderModal').classList.add('active');
        }

        function updateReorderTotal() {
            const qty = parseInt(document.getElementById('reorder_quantity').value) || 0;
            const total = reorderUnitPrice * qty;
            document.getElementById('reorder_total').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(total);
        }

        function closeReorderModal() {
            document.getElementById('reorderModal').classList.remove('active');
        }

        // event listener: update total saat qty berubah
        document.getElementById('reorder_quantity').addEventListener('input', updateReorderTotal);

        // Tutup modal dengan klik luar
        document.getElementById('reorderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReorderModal();
            }
        });

        // Tutup modal dengan ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReorderModal();
            }
        });
    </script>

</body>

</html>