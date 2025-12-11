<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keranjang Consumable</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/customer/consumable/consum_cart.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../../views/layout/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../../views/layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">Keranjang Consumable</h1>
                        <p class="page-subtitle">
                           Daftar produk yang Anda pilih dari katalog
                        </p>
                    </div>


                    <?php if (empty($items)): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-basket fa-3x"></i>
                            <h4>Keranjang Kosong</h4>
                            <p>Belum ada produk yang ditambahkan ke keranjang.</p>
                            <a href="<?= $basePath ?>/admin/consumable/sections" class="btn-primary">Lihat Katalog</a>
                        </div>
                    <?php else: ?>
                        <form method="post" action="<?= $basePath ?>/customer/consumable/cart/checkout">
                            <div class="select-all-bar">
                                <input type="checkbox" id="select_all">
                                <label for="select_all">Pilih Semua</label>
                            </div>

                            <div class="cart-container">
                                <div class="cart-items">
                                    <div class="cart-header">
                                        <div>Pilih</div>
                                        <div>Produk</div>
                                        <div>Harga</div>
                                        <div>Jumlah</div>
                                        <div>Subtotal</div>
                                        <div></div>
                                    </div>

                                    <?php foreach ($items as $item): ?>
                                        <?php $subtotal = $item['price'] * $item['quantity']; ?>
                                        <div class="cart-item"
                                            data-unit-price="<?= (float)$item['price'] ?>"
                                            data-cart-id="<?= $item['id'] ?>">

                                            <!-- Checkbox -->
                                            <div class="item-checkbox">
                                                <input type="checkbox" name="selected_items[]" value="<?= $item['id'] ?>">
                                            </div>

                                            <!-- Produk -->
                                            <div class="item-product">
                                                <div class="item-image">
                                                    <?php if (!empty($item['image_path'])): ?>
                                                        <img src="<?= $basePath . '/uploads/consum-katalog-item/' . basename($item['image_path']) ?>"
                                                            alt="<?= htmlspecialchars($item['name']) ?>">
                                                    <?php else: ?>
                                                        <i class="fas fa-box"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="item-details">
                                                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                                                    <small>Jenis Produk: <?= htmlspecialchars($item['product_type'] ?? '-') ?></small>
                                                    <?php if (!empty($item['file_path'])): ?>
                                                        <a href="<?= $basePath . htmlspecialchars($item['file_path']) ?>" target="_blank">
                                                            <i class="fas fa-file-pdf"></i> File Drawing
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="no-drawing"><i class="fas fa-file"></i> Tidak ada drawing</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Harga -->
                                            <div class="item-price">
                                                Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                            </div>

                                            <!-- Qty control -->
                                            <div class="quantity-control">
                                                <button type="button" class="qty-btn qty-decrease" data-cart-id="<?= $item['id'] ?>">−</button>
                                                <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" min="1" data-cart-id="<?= $item['id'] ?>">
                                                <button type="button" class="qty-btn qty-increase" data-cart-id="<?= $item['id'] ?>">+</button>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="item-subtotal">
                                                Rp <?= number_format($subtotal, 0, ',', '.') ?>
                                            </div>

                                            <!-- Hapus -->
                                            <div>
                                                <a href="<?= $basePath ?>/customer/consumable/cart/delete?id=<?= $item['id'] ?>"
                                                    class="item-remove"
                                                    onclick="return confirm('Hapus item ini dari keranjang?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Ringkasan Pesanan -->
                                <div class="order-summary">
                                    <h3 class="summary-title">Ringkasan Pesanan</h3>
                                    <div class="summary-row">
                                        <span>Jumlah Produk</span>
                                        <span id="selected_count">0</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Subtotal</span>
                                        <span id="selected_subtotal">Rp 0</span>
                                    </div>
                                    <div class="summary-row total">
                                        <span>Total</span>
                                        <span class="amount" id="selected_total">Rp 0</span>
                                    </div>
                                    <button type="submit" class="checkout-btn" id="checkout-btn" disabled>
                                        Lanjutkan ke Checkout
                                    </button>
                                    <a href="<?= $basePath ?>/shared/consumable/product-items/" class="continue-shopping">
                                        ← Kembali ke Katalog
                                    </a>
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
            const checkboxes = document.querySelectorAll('.item-checkbox input[type="checkbox"]');
            const countEl = document.getElementById('selected_count');
            const subtotalEl = document.getElementById('selected_subtotal');
            const totalEl = document.getElementById('selected_total');
            const checkoutBtn = document.getElementById('checkout-btn');

            const formatRp = function(n) {
                return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID');
            };

            function updateSummary() {
                let itemCount = 0;
                let subtotal = 0;

                checkboxes.forEach(function(cb) {
                    const row = cb.closest('.cart-item');
                    const unitPrice = Number(row.dataset.unitPrice) || 0;
                    const qty = Math.max(1, parseInt(row.querySelector('.qty-input').value, 10) || 1);

                    if (cb.checked) {
                        itemCount++;
                        subtotal += unitPrice * qty;
                    }

                    // update subtotal per baris
                    const rowSubtotal = row.querySelector('.item-subtotal');
                    if (rowSubtotal) rowSubtotal.textContent = formatRp(unitPrice * qty);
                });

                countEl.textContent = itemCount;
                subtotalEl.textContent = formatRp(subtotal);
                totalEl.textContent = formatRp(subtotal);
                checkoutBtn.disabled = itemCount === 0;

                if (selectAll) {
                    const checkedCount = document.querySelectorAll('.item-checkbox input[type="checkbox"]:checked').length;
                    selectAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
                }
            }

            // Event listener untuk checkbox
            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', updateSummary);
            });

            // Event listener untuk qty input
            document.querySelectorAll('.qty-input').forEach(function(input) {
                input.addEventListener('change', updateSummary);
                input.addEventListener('input', updateSummary);
            });

            // Event listener untuk select all
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(function(cb) {
                        cb.checked = selectAll.checked;
                    });
                    updateSummary();
                });
            }

            // Handler untuk tombol decrease quantity
            document.querySelectorAll('.qty-decrease').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const cartId = this.dataset.cartId;
                    const qtyInput = document.querySelector('.qty-input[data-cart-id="' + cartId + '"]');
                    const currentQty = parseInt(qtyInput.value);
                    const newQty = Math.max(1, currentQty - 1);

                    if (newQty === currentQty) return;

                    qtyInput.value = newQty;
                    updateSummary();

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?= $basePath ?>/customer/consumable/cart/update', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function() {
                        if (xhr.status !== 200) {
                            console.error('Error updating quantity');
                            location.reload();
                        }
                    };

                    xhr.onerror = function() {
                        console.error('Request failed');
                        location.reload();
                    };

                    xhr.send('id=' + cartId + '&quantity=' + newQty);
                });
            });

            // Handler untuk tombol increase quantity
            document.querySelectorAll('.qty-increase').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const cartId = this.dataset.cartId;
                    const qtyInput = document.querySelector('.qty-input[data-cart-id="' + cartId + '"]');
                    const currentQty = parseInt(qtyInput.value);
                    const newQty = currentQty + 1;

                    // Update UI langsung
                    qtyInput.value = newQty;
                    updateSummary();

                    // Kirim ke server menggunakan XMLHttpRequest (PHP 7 compatible)
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?= $basePath ?>/customer/consumable/cart/update', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function() {
                        if (xhr.status !== 200) {
                            console.error('Error updating quantity');
                            // Rollback jika error
                            location.reload();
                        }
                    };

                    xhr.onerror = function() {
                        console.error('Request failed');
                        location.reload();
                    };

                    xhr.send('id=' + cartId + '&qty=' + newQty);
                });
            });

            // Initial update
            updateSummary();
        });
    </script>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>