<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Printilan Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/consumable/product_item.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <div class="page-header">
                        <h1 class="page-title"><i class="fas fa-cogs"></i> Printilan Produk dari <?= strtoupper(htmlspecialchars($productType['name'])) ?></h1>
                        <p class="page-subtitle">Daftar printilan yang termasuk dalam jenis produk ini</p>
                    </div>

                    <!-- Admin: tombol tambah item -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div class="mb-3">
                            <a href="<?= $basePath ?>/admin/consumable/product-items/create/<?= $productType['id'] ?>" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Printilan
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Empty state -->
                    <?php if (empty($items)): ?>
                        <div class="empty-state text-center mt-4">
                            <i class="fas fa-cube fa-2x text-muted"></i>
                            <h4 class="mt-2">Belum Ada Printilan</h4>
                            <p>Jenis produk ini belum memiliki printilan.</p>
                        </div>
                    <?php else: ?>
                        <div class="product-items-container d-flex flex-wrap gap-3">
                            <?php foreach ($items as $item): ?>
                                <div class="product-item-card">
                                    <div class="product-item-image"
                                        style="background-image:url('<?= $item['image_path'] ?: $basePath . '/assets/img/default.jpeg' ?>');">
                                    </div>
                                    <div class="product-item-content">
                                        <h4 class="product-item-name"><?= htmlspecialchars($item['name']) ?></h4>
                                        <p class="product-item-code">Kode: <?= htmlspecialchars($item['item_code']) ?></p>
                                        <p class="product-item-price">
                                            <?= $item['price'] === null ? 'Harga belum ditentukan' : 'Rp ' . number_format($item['price'], 0, ',', '.') ?>
                                        </p>

                                        <div class="product-item-actions mt-2">
                                            <?php if ($currentRole === 'admin'): ?>
                                                <a href="<?= $basePath ?>/admin/consumable/product-items/edit/<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="<?= $basePath ?>/admin/consumable/product-items/delete/<?= $item['id'] ?>" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin hapus printilan ini?');">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            <?php elseif ($currentRole === 'customer'): ?>
                                                <a href="<?= $basePath ?>/customer/consumable/product-item?id=<?= $item['id'] ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Lihat Detail
                                                </a>
                                                <a href="<?= $basePath ?>/customer/cart/add?item=<?= $item['id'] ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-shopping-cart"></i> Pesan
                                                </a>
                                            <?php elseif ($currentRole === 'spv'): ?>
                                                <a href="<?= $basePath ?>/spv/consumable/product-item?id=<?= $item['id'] ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Lihat Detail
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>