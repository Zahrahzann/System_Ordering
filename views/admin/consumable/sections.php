<?php
$basePath = '/system_ordering/public';
session_start();
$currentRole = $_SESSION['user_data']['role'] ?? null;
$isEditMode = isset($_GET['edit']) && isset($editData);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Consumable</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/consumable/section.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <div class="page-header">
                        <h1 class="page-title"><i class="fas fa-shopping-bag"></i> Katalog Consumable</h1>
                        <p class="page-subtitle">Pilih section untuk melihat produk yang tersedia</p>
                    </div>

                    <?php if ($currentRole === 'admin'): ?>
                        <div class="mb-3">
                            <a href="<?= $basePath ?>/admin/consumable/sections?create=1" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Section
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <div><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($_GET['create']) || $isEditMode): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <form method="POST" action="<?= $basePath ?>/admin/consumable/sections/<?= $isEditMode ? 'edit' : 'add' ?>">
                                    <?php if ($isEditMode): ?>
                                        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label>Nama Section</label>
                                        <input type="text" name="name" class="form-control" required
                                            value="<?= $isEditMode ? htmlspecialchars($editData['name']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="description" class="form-control"><?= $isEditMode ? htmlspecialchars($editData['description']) : '' ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-<?= $isEditMode ? 'primary' : 'success' ?>">
                                        <i class="fas fa-<?= $isEditMode ? 'save' : 'plus' ?>"></i>
                                        <?= $isEditMode ? 'Update Section' : 'Tambah Section' ?>
                                    </button>
                                    <a href="<?= $basePath ?>/admin/consumable/sections" class="btn btn-secondary ml-2">Batal</a>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($sections)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h4>Belum Ada Section</h4>
                            <p>Tidak ada section produk yang tersedia saat ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="sections-container">
                            <?php
                            $icons = ['box-open', 'tools', 'hard-hat', 'cogs', 'screwdriver', 'wrench'];
                            $index = 0;
                            foreach ($sections as $sec):
                                $icon = $icons[$index % count($icons)];
                                $index++;
                            ?>
                                <div class="section-card">
                                    <div class="section-image" style="background-image:url('<?= $basePath ?>/assets/img/section.jpeg');"></div>
                                    <div class="section-content">
                                        <div class="item-count"><i class="fas fa-cube"></i><span>12 items</span></div>
                                        <div class="section-icon"><i class="fas fa-<?= $icon ?>"></i></div>
                                        <div class="section-info">
                                            <div class="section-name"><?= strtoupper(htmlspecialchars($sec['name'])) ?></div>
                                            <div class="section-desc"><?= htmlspecialchars($sec['description'] ?? 'Klik untuk melihat semua produk dalam section ' . $sec['name']) ?></div>
                                            <a href="<?= $basePath ?>/customer/consumable/catalog?section=<?= urlencode($sec['id']) ?>" class="view-button">
                                                <i class="fas fa-arrow-right"></i><span> Lihat Produk</span>
                                            </a>
                                            <?php if ($currentRole === 'admin'): ?>
                                                <div class="section-actions">
                                                    <a href="<?= $basePath ?>/admin/consumable/sections?edit=<?= $sec['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete(<?= $sec['id'] ?>)" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
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

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
    <script>
        function confirmDelete(id) {
            if (confirm("Yakin ingin menghapus section ini?")) {
                window.location.href = "<?= $basePath ?>/admin/consumable/sections/delete?id=" + id;
            }
        }
    </script>
</body>

</html>