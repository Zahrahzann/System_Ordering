<?php
if (!isset($categories)) die('Controller tidak menyediakan data kategori.');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Consumable</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/customer/consumable/catalog_category.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
    <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include __DIR__ . '/../../layout/topbar.php'; ?>
            <div class="container-fluid">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-shopping-bag"></i>
                        Katalog Consumable
                    </h1>
                    <p class="page-subtitle">Pilih kategori untuk melihat produk yang tersedia</p>
                </div>

                <?php if (empty($categories)): ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h4>Belum Ada Kategori</h4>
                        <p>Tidak ada kategori produk yang tersedia saat ini.</p>
                    </div>
                <?php else: ?>
                    <div class="categories-container">
                        <?php 
                        $icons = ['box-open', 'tools', 'hard-hat', 'cogs', 'screwdriver', 'wrench'];
                        $index = 0;
                        foreach ($categories as $cat): 
                            $icon = $icons[$index % count($icons)];
                            $index++;
                        ?>
                            <div class="category-card">
                                <div class="category-content">
                                    <div class="item-count">
                                        <i class="fas fa-cube"></i>
                                        <span>12 items</span>
                                    </div>

                                    <div class="category-icon">
                                        <i class="fas fa-<?= $icon ?>"></i>
                                    </div>

                                    <div class="category-info">
                                        <div class="category-name"><?= strtoupper(htmlspecialchars($cat)) ?></div>
                                        <div class="category-desc">
                                            Klik untuk melihat semua produk dalam kategori <?= htmlspecialchars($cat) ?>. Tersedia berbagai pilihan berkualitas untuk kebutuhan Anda.
                                        </div>

                                        <a href="/system_ordering/public/customer/consumable/catalog?category=<?= urlencode($cat) ?>" 
                                           class="view-button">
                                            <i class="fas fa-arrow-right"></i>
                                            <span>Lihat Produk</span>
                                        </a>
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

<script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
<script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>
</body>
</html>