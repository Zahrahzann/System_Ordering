<?php
if (!isset($categories)) die('Controller tidak menyediakan data kategori.');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Jalur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/admin/consumable/kategori.css?v=<?= time() ?>" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <div class="page-header">
                        <div class="page-header-left">
                            <h1>
                                <i class="fas fa-shopping-bag"></i>
                                Katalog Jalur Consumable
                            </h1>
                            <p class="page-subtitle">Kelola jalur untuk produk consumable</p>
                        </div>
                        <button class="btn-add" onclick="document.getElementById('addForm').style.display='block'">
                            <i class="fas fa-plus"></i> Buat Consumable
                        </button>
                    </div>

                    <!-- Form Tambah Category -->
                    <div id="addForm" style="display:none;" class="form-card">
                        <form action="/admin/consumable/katalog_kategori/add" method="POST">
                            <div class="form-group">
                                <label for="name">Nama Jalur</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Masukkan nama kategori">
                            </div>
                            <button type="submit" class="btn-success">
                                <i class="fas fa-check"></i> Simpan
                            </button>
                            <button type="button" class="btn-secondary" onclick="document.getElementById('addForm').style.display='none'">
                                Batal
                            </button>
                        </form>
                    </div>

                    <?php if (empty($categories)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h4>Belum Ada Jalur yang mendaftar</h4>
                            <p>Tidak ada jalur produk yang tersedia saat ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="categories-container">
                            <?php foreach ($categories as $cat): ?>
                                <div class="category-card">
                                    <div class="category-image"
                                        style="background-image: url('/system_ordering/public/assets/img/kategori.jpeg');">
                                    </div>
                                    <div class="category-content">
                                        <div class="item-count">
                                            <i class="fas fa-cube"></i>
                                            <span><?= $cat['item_count'] ?? '0' ?> items</span>
                                        </div>

                                        <div class="category-info">
                                            <div class="category-name"><?= strtoupper(htmlspecialchars($cat['name'])) ?></div>
                                            <div class="category-desc">
                                                Klik untuk melihat semua produk dalam jalur <?= htmlspecialchars($cat['name']) ?>.
                                            </div>

                                            <div class="category-actions">
                                                <a href="/admin/consumable/katalog_kategori/edit?id=<?= $cat['id'] ?>" class="btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="/admin/consumable/katalog_kategori/delete?id=<?= $cat['id'] ?>"
                                                    class="btn-sm btn-danger"
                                                    onclick="return confirm('Yakin hapus kategori ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                                <a href="/admin/consumable/katalog_produk?category=<?= urlencode($cat['name']) ?>"
                                                    class="btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Lihat Produk
                                                </a>
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

    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>
</body>

</html>