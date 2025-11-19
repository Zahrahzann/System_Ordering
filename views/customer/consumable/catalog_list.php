<?php
if (!isset($products)) die('Controller tidak menyediakan data produk.');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Katalog Consumable</title>
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/customer/consumable/catalog.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../layout/topbar.php'; ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">SLIPPER <?= strtoupper(htmlspecialchars($_GET['category'])) ?></h1>

                    <?php if (empty($products)): ?>
                        <div class="empty-cart">
                            <i class="fas fa-box"></i>
                            <h3>Belum ada produk di kategori ini</h3>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-lg-4">
                                    <div class="menu-card">
                                        <div class="menu-card-header">
                                            <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>" style="max-height: 180px;">
                                        </div>
                                        <div class="menu-card-body">
                                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                                            <p><?= htmlspecialchars($product['description']) ?></p>
                                            <div class="card-badge"><?= htmlspecialchars($_GET['category']) ?></div>
                                            <div class="menu-card-footer">
                                                <div class="card-action">
                                                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                                                </div>
                                                <div class="card-action">
                                                    <a href="/customer/consumable/cart/add?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                                    </a>
                                                    <a href="/customer/consumable/buy?id=<?= $product['id'] ?>" class="btn btn-success btn-sm">
                                                        <i class="fas fa-bolt"></i> Buy Now
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php include __DIR__ . '/../../layout/footer.php'; ?>
        </div>
    </div>
</body>

</html>