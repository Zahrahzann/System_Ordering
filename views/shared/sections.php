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
    <link href="<?= $basePath ?>/assets/css/admin/consumable/section.css?v=<?= time() ?>" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <div class="page-header">
                        <h1 class="page-title"><i class="fas fa-shopping-bag"></i> Katalog Consumable</h1>
                        <p class="page-subtitle">Pilih section untuk melihat produk yang tersedia</p>
                    </div>

                    <!-- Admin: tombol toggle form -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div class="mb-3">
                            <button class="btn btn-info" onclick="toggleForm()">
                                <i class="fas fa-plus"></i> Tambah Section
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Error messages -->
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <div><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <!-- Form Membuat/edit section (admin only) -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div id="sectionForm" class="card mb-4" style="<?= !$isEditMode ? 'display:none;' : '' ?>">
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
                                    <button type="submit" class="btn btn-<?= $isEditMode ? 'primary' : 'success' ?>">
                                        <i class="fas fa-<?= $isEditMode ? 'save' : 'plus' ?>"></i>
                                        <?= $isEditMode ? 'Update Section' : 'Tambah Section' ?>
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="toggleForm()">Batal</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- List sections -->
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
                                            <div class="section-desc">
                                                Klik untuk melihat semua produk dalam section.
                                            </div>
                                            <a href="<?= $basePath ?>/shared/consumable/product-types/<?= urlencode($sec['id']) ?>" class="view-button">
                                                <i class="fas fa-arrow-right"></i><span> Lihat Produk</span>
                                            </a>

                                            <!-- Admin: edit/delete -->
                                            <?php if ($currentRole === 'admin'): ?>
                                                <div class="section-actions">
                                                    <a href="<?= $basePath ?>/admin/consumable/sections?edit=<?= $sec['id'] ?>">
                                                        <i class="fas fa-edit"> Edit</i>
                                                    </a>
                                                    <button onclick="confirmDelete(<?= $sec['id'] ?>)">
                                                        <i class="fas fa-trash"> Hapus</i>
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
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
    <script>
        function toggleForm() {
            const form = document.getElementById('sectionForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function confirmDelete(id) {
            if (confirm("Yakin ingin menghapus section ini?")) {
                window.location.href = "<?= $basePath ?>/admin/consumable/sections/delete?id=" + id;
            }
        }

        function addToCart(sectionId) {
            window.location.href = "<?= $basePath ?>/customer/cart/add?section=" + sectionId;
        }

        function orderNow(sectionId) {
            window.location.href = "<?= $basePath ?>/customer/order/create?section=" + sectionId;
        }
    </script>
</body>

</html>