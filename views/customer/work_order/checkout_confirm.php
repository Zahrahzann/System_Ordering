<?php
// File ini hanya untuk tampilan, datanya dikirim dari CartController.
if (!isset($cartItems)) die('Controller tidak bisa menyediakan data untuk halaman konfirmasi.');
$basePath = '/system_ordering/public';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/customer/work_order/confirm_checkout.css" rel="stylesheet">
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
                        <h1 class="page-title">Konfirmasi Work Order</h1>
                        <p class="page-subtitle">
                            Konfirmasi checkout semua item sebelum mengirim pesanan untuk approval SPV
                        </p>
                    </div>

                    <!-- Info Alert -->
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        <p>Harap periksa kembali item di bawah ini sebelum mengirim pesanan untuk approval SPV. Pastikan semua informasi sudah benar.</p>
                    </div>

                    <!-- Summary Card -->
                    <div class="summary-card">
                        <div class="summary-header">
                            <div class="summary-title">
                                <i class="fas fa-boxes"></i>
                                Item yang akan di-checkout
                            </div>
                            <div class="item-count"><?= count($cartItems) ?> Item</div>
                        </div>

                        <div class="items-container">
                            <?php foreach ($cartItems as $index => $item): ?>
                                <div class="item-card">
                                    <div class="item-header">
                                        <div>
                                            <div class="item-number">Item #<?= $index + 1 ?></div>
                                            <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                        </div>
                                        <div>
                                            <?php
                                            if ($item['is_emergency']) {
                                                echo $item['emergency_type'] === 'line_stop'
                                                    ? '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Line Stop</span>'
                                                    : '<span class="badge badge-success"><i class="fas fa-shield-alt"></i> Safety</span>';
                                            } else {
                                                echo '<span class="badge badge-info"><i class="fas fa-check"></i> Regular</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="item-details-grid">
                                        <div class="detail-item">
                                            <div class="detail-label">Kategori</div>
                                            <div class="detail-value"><?= htmlspecialchars($item['category']) ?></div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Jenis Material</div>
                                            <div class="detail-value"><?= htmlspecialchars($item['material_type'] ?? '-') ?></div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Dimensi Material</div>
                                            <div class="detail-value"><?= htmlspecialchars($item['material_dimension'] ?? '-') ?></div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Quantity</div>
                                            <div class="detail-value"><?= htmlspecialchars($item['quantity']) ?> Unit</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Tanggal Dibutuhkan</div>
                                            <div class="detail-value">
                                                <i class="far fa-calendar" style="color: #667eea;"></i>
                                                <?= date('d M Y', strtotime($item['needed_date'])) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $files = json_decode($item['file_path'], true);
                                    if (is_array($files) && !empty($files)):
                                    ?>
                                        <div class="files-section">
                                            <div class="files-label">File Drawing</div>
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
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="action-footer">
                        <div class="total-info">
                            <div class="total-label">Total Item</div>
                            <div class="total-value"><?= count($cartItems) ?></div>
                        </div>

                        <div class="action-buttons">
                            <a href="<?= $basePath ?>/customer/cart" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>

                            <form action="<?= $basePath ?>/customer/checkout/process" method="POST" style="display: inline;" onsubmit="return confirm('Anda yakin ingin mengirim semua item ini untuk approval SPV?')">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Confirm & Checkout Now!
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>
</body>

</html>