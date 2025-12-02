<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath   = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? null;
$editData   = $editData ?? null;
$isEditMode = isset($_GET['edit']) && $editData !== null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Katalog Consumable</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/consumable/jenis_produk.css?v=<?= time() ?>" rel="stylesheet">

</head>
<html>

<body>
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-boxes"></i> Jenis Produk di Section <?= strtoupper(htmlspecialchars($section['name'])) ?></h1>
        <p class="page-subtitle">Pilih jenis produk untuk melihat detail barang</p>
    </div>

    <div class="product-types-container">
        <?php foreach ($productTypes as $pt): ?>
            <div class="product-type-card" onclick="window.location.href='<?= $basePath ?>/customer/consumable/product-type?id=<?= $pt['id'] ?>'">
                <div class="product-type-image" style="background-image:url('<?= $pt['image_path'] ?: $basePath . '/assets/img/default.jpeg' ?>');"></div>
                <div class="product-type-content">
                    <h4 class="product-type-name"><?= htmlspecialchars($pt['name']) ?></h4>
                    <small class="product-type-section">Section: <?= htmlspecialchars($section['name']) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

</body>

</html>