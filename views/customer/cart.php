<?php
if (!isset($cartItems)) die('Controller tidak menyediakan data keranjang.');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keranjang Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/customer/work_order/cart.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Keranjang Work Order</h1>

                    <?php if (empty($cartItems)): ?>
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Keranjang Anda Masih Kosong</h3>
                            <p>Tambahkan item work order untuk melanjutkan</p>
                        </div>
                    <?php else: ?>
                        <form action="/system_ordering/public/customer/checkout/confirm" method="POST">
                            <div class="row">
                                <!-- Daftar Item -->
                                <div class="col-lg-8">
                                    <!-- Pilih Semua -->
                                    <div class="select-all-bar">
                                        <input type="checkbox" id="select_all">
                                        <label for="select_all">Pilih Semua</label>
                                    </div>

                                    <?php foreach ($cartItems as $item): ?>
                                        <div class="cart-item">
                                            <div class="cart-item-content">
                                                <!-- Checkbox per item -->
                                                <div class="item-checkbox-wrapper">
                                                    <input type="checkbox" class="item-checkbox" name="selected_items[]" value="<?= $item['id'] ?>">
                                                </div>

                                                <!-- Icon / gambar item -->
                                                <div class="item-image">
                                                    <i class="fas fa-tools"></i>
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

                                                    <!-- File Drawing -->
                                                    <?php
                                                    $files = json_decode($item['file_path'], true);
                                                    if (is_array($files) && !empty($files)):
                                                    ?>
                                                        <div class="item-files">
                                                            <div class="files-label">File Drawing:</div>
                                                            <?php foreach ($files as $file):
                                                                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                                $icon = 'fa-file-alt';
                                                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                                    $icon = 'fa-file-image';
                                                                } elseif ($extension === 'pdf') {
                                                                    $icon = 'fa-file-pdf';
                                                                }
                                                            ?>
                                                                <a href="<?= htmlspecialchars($file) ?>" target="_blank" class="file-link">
                                                                    <i class="fas <?= $icon ?>"></i>
                                                                    <?= basename($file) ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Footer item -->
                                                    <div class="item-footer">
                                                        <div class="item-meta">
                                                            <div class="qty-info">
                                                                Qty: <strong><?= htmlspecialchars($item['quantity']) ?></strong>
                                                            </div>
                                                            <div class="date-info">
                                                                <i class="far fa-calendar"></i>
                                                                <?= date('d M Y', strtotime($item['needed_date'])) ?>
                                                            </div>
                                                            <div>
                                                                <?php
                                                                if ($item['is_emergency']) {
                                                                    echo $item['emergency_type'] === 'line_stop'
                                                                        ? '<span class="badge badge-danger">Line Stop</span>'
                                                                        : '<span class="badge badge-success">Safety</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-info">Regular</span>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>

                                                        <div class="item-actions">
                                                            <a href="<?= $basePath ?>/customer/cart/edit/<?= $item['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <a href="<?= $basePath ?>/customer/cart/delete/<?= $item['id'] ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Ringkasan -->
                                <div class="col-lg-4">
                                    <div class="checkout-summary">
                                        <h5 style="margin-bottom: 15px; font-weight: 600;">Ringkasan Pesanan</h5>
                                        <div class="summary-row">
                                            <span class="summary-label">Item yang dipilih:</span>
                                            <span class="summary-value" id="selected_count">0</span>
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="checkout-btn" disabled>
                                            <i class="fas fa-check mr-2"></i> Checkout Sekarang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select_all');
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const selectedCount = document.getElementById('selected_count');
            const checkoutBtn = document.getElementById('checkout-btn');

            function updateState() {
                let itemCount = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        itemCount++;
                    }
                });

                // update jumlah item terpilih
                selectedCount.textContent = itemCount;

                // aktifkan tombol checkout kalau ada item dipilih
                checkoutBtn.disabled = itemCount === 0;

                // sinkronkan "Pilih Semua"
                if (selectAll) {
                    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                    selectAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateState();
                });
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateState));
            updateState();
        });
    </script>

    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>
</body>

</html>