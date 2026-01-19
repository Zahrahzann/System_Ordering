<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';

// variabel dari controller
$editData   = $editData ?? null;
$isEditMode = $editData !== null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Katalog Consumable - Jenis Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/consumable/product-type.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">Jenis Slipper</h1>
                        <p class="page-subtitle">
                            Daftar Jenis Slipper yang termasuk dalam Section <?= htmlspecialchars($section['name']) ?>
                        </p>
                    </div>

                    <!-- Admin: tombol toggle form -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div class="mb-3">
                            <button class="btn btn-info" onclick="toggleForm()">
                                <i class="fas fa-plus"></i> <?= $isEditMode ? 'Edit Jenis Slipper' : 'Tambah Jenis Slipper' ?>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Form create/edit (admin only) -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div id="productTypeForm" class="card mb-4" style="<?= !$isEditMode ? 'display:none;' : '' ?>">
                            <div class="card-body">
                                <form method="POST" action="<?= $basePath ?>/admin/consumable/product-types/<?= $isEditMode ? 'edit/' . $editData['id'] : 'create/' . $section['id'] ?>" enctype="multipart/form-data">
                                    <?php if ($isEditMode): ?>
                                        <input type="hidden" name="section_id" value="<?= htmlspecialchars($editData['section_id']) ?>">
                                    <?php endif; ?>
                                    <?php if ($isEditMode): ?>
                                        <div class="form-group">
                                            <label>Kode Slipper</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($editData['product_code']) ?>" readonly>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label>Nama Slipper</label>
                                        <input type="text" name="name" class="form-control" required
                                            value="<?= $isEditMode ? htmlspecialchars($editData['name']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="description" class="form-control"><?= $isEditMode ? htmlspecialchars($editData['description']) : '' ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Foto Sampul</label>
                                        <input type="file" name="image" class="form-control">
                                        <?php if ($isEditMode && !empty($editData['image_path'])): ?>
                                            <small>Gambar lama: <?= htmlspecialchars($editData['image_path']) ?></small>
                                            <input type="hidden" name="old_image" value="<?= htmlspecialchars($editData['image_path']) ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Drawing</label>
                                        <input type="file" name="file_path" class="form-control">
                                        <?php if ($isEditMode && !empty($editData['file_path'])): ?>
                                            <small>File lama: <?= htmlspecialchars($editData['file_path']) ?></small>
                                            <input type="hidden" name="old_file" value="<?= htmlspecialchars($editData['file_path']) ?>">
                                        <?php endif; ?>
                                    </div>
                                    <button type="submit" class="btn btn-<?= $isEditMode ? 'primary' : 'success' ?>">
                                        <i class="fas fa-<?= $isEditMode ? 'save' : 'plus' ?>"></i>
                                        <?= $isEditMode ? 'Update Jenis Produk' : 'Tambah Jenis Produk' ?>
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="toggleForm()">Batal</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Empty state -->
                    <?php if (empty($productTypes)): ?>
                        <div class="empty-state text-center mt-4">
                            <i class="fas fa-cube fa-2x text-muted"></i>
                            <h4 class="mt-2">Belum Ada Jenis Slipper</h4>
                            <p>Section ini belum memiliki jenis slipper.</p>
                        </div>
                    <?php else: ?>
                        <div class="product-types-container d-flex flex-wrap gap-3">
                            <?php foreach ($productTypes as $pt): ?>
                                <a href="<?= $basePath ?>/shared/consumable/product-items/<?= $pt['id'] ?>" class="product-type-card-link">
                                    <div class="product-type-card">
                                        <div class="product-type-image"
                                            style="background-image:url('<?= $basePath . (!empty($pt['image_path']) ? $pt['image_path'] : '/assets/img/default.jpeg') ?>');">
                                        </div>
                                        <div class="product-type-content">
                                            <h4 class="product-type-name"><?= htmlspecialchars($pt['name']) ?></h4>
                                            <p class="product-type-code">Kode: <?= htmlspecialchars($pt['product_code']) ?></p>
                                            <small class="product-type-section">Section: <?= htmlspecialchars($section['name']) ?></small>

                                            <div class="product-type-actions mt-2">
                                                <?php if ($currentRole === 'admin'): ?>
                                                    <a href="<?= $basePath ?>/admin/consumable/product-types/edit/<?= $pt['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="<?= $basePath ?>/admin/consumable/product-types/delete/<?= $pt['id'] ?>" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin hapus jenis produk ini?');">
                                                        <i class="fas fa-trash"></i> Hapus
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
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
    <script>
        function toggleForm() {
            const form = document.getElementById('productTypeForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>

</html>