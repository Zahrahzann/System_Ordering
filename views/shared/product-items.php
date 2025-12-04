<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? 'customer';

$productTypeName = !empty($productType['name']) ? strtoupper(htmlspecialchars($productType['name'])) : 'Unknown';
$productTypeId   = $productType['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Printilan Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/shared/katalog_item.css?v=<?= time() ?>" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-cogs"></i> Printilan Produk dari <?= $productTypeName ?>
                        </h1>
                        <p class="page-subtitle">Daftar printilan yang termasuk dalam jenis produk ini</p>
                    </div>

                    <!-- Admin: tombol tambah item -->
                    <?php if ($currentRole === 'admin' && $productTypeId): ?>
                        <div class="mb-3">
                            <button class="btn btn-success" data-toggle="modal" data-target="#itemModal">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Empty state -->
                    <?php if (empty($items)): ?>
                        <div class="empty-state">
                            <i class="fas fa-cube"></i>
                            <h4>Belum Ada Printilan</h4>
                            <p>Jenis produk ini belum memiliki printilan.</p>
                        </div>
                    <?php else: ?>
                        <div class="product-items-container">
                            <?php foreach ($items as $item): ?>
                                <div class="product-item-card">
                                    <!-- Product Image with Badge -->
                                    <div class="product-item-image"
                                        style="background-image:url('<?= !empty($item['image_path']) ? $basePath . htmlspecialchars($item['image_path']) : $basePath . '/assets/img/default.jpeg' ?>');">
                                        <span class="category-badge"></span>
                                    </div>

                                    <div class="product-item-content">
                                        <h4 class="product-item-name"><?= htmlspecialchars($item['name']) ?></h4>

                                        <?php if ($currentRole === 'admin'): ?>
                                            <div class="form-group">
                                                <label>Stok</label>
                                                <input type="number" name="stock" id="itemStock" class="form-control" min="0">
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($currentRole === 'admin'): ?>
                                            <div class="stock-status">
                                                <div class="stock-bar 
            <?php
                                            if ($item['stock'] < 10) echo 'stock-low';
                                            elseif ($item['stock'] < 20) echo 'stock-medium';
                                            else echo 'stock-high';
            ?>"
                                                    style="width:<?= min(100, $item['stock']) ?>%">
                                                </div>
                                                <small>Stok: <?= $item['stock'] ?></small>
                                            </div>
                                        <?php endif; ?>

                                        <!-- File Drawing -->
                                        <?php if (!empty($item['file_path'])): ?>
                                            <a href="<?= $basePath . htmlspecialchars($item['file_path']) ?>" target="_blank" class="file-link">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>Drawing File</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="file-link disabled">
                                                <i class="fas fa-file"></i>
                                                <span>No Drawing</span>
                                            </span>
                                        <?php endif; ?>

                                        <p class="product-item-price">
                                            <?= $item['price'] === null ? 'TBA' : 'Rp ' . number_format($item['price'], 0, ',', '.') ?>
                                        </p>

                                        <?php if ($currentRole === 'customer'): ?>
                                            <!-- Quantity Control -->
                                            <div class="quantity-control">
                                                <label>Qty:</label>
                                                <div class="qty-wrapper">
                                                    <button type="button" class="qty-btn qty-minus">-</button>
                                                    <input type="number" class="qty-input" value="1" min="1" data-item-id="<?= $item['id'] ?>">
                                                    <button type="button" class="qty-btn qty-plus">+</button>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="product-item-actions">
                                                <button class="btn btn-outline" onclick="addToCart(<?= $item['id'] ?>)">
                                                    Add to Cart
                                                </button>
                                                <button class="btn btn-dark" onclick="buyNow(<?= $item['id'] ?>)">
                                                    Buy Now
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($currentRole === 'admin'): ?>
                                            <div class="admin-actions">
                                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#itemModal"
                                                    data-id="<?= $item['id'] ?>"
                                                    data-name="<?= htmlspecialchars($item['name']) ?>"
                                                    data-price="<?= $item['price'] ?>"
                                                    data-description="<?= htmlspecialchars($item['description'] ?? '') ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="<?= $basePath ?>/admin/consumable/product-items/delete/<?= $item['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin hapus printilan ini?');">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="itemForm" action="<?= $basePath ?>/admin/consumable/product-items/create/<?= $productTypeId ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Printilan Produk</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="itemId">

                        <div class="form-group">
                            <label>Nama Printilan</label>
                            <input type="text" name="name" id="itemName" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Harga</label>
                            <input type="number" name="price" id="itemPrice" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" id="itemDescription" class="form-control"></textarea>
                        </div>

                        <?php if ($currentRole === 'admin'): ?>
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stock" id="itemStock" class="form-control" min="0">
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Gambar</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>File Tambahan</label>
                            <input type="file" name="file_path" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background: #667eea;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Quantity controls
        document.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                input.value = parseInt(input.value) + 1;
            });
        });

        document.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });

        // Add to cart function
        function addToCart(itemId) {
            const qtyInput = document.querySelector(`input[data-item-id="${itemId}"]`);
            const quantity = qtyInput ? qtyInput.value : 1;
            window.location.href = `<?= $basePath ?>/customer/cart/add?item=${itemId}&qty=${quantity}`;
        }

        // Buy now function
        function buyNow(itemId) {
            const qtyInput = document.querySelector(`input[data-item-id="${itemId}"]`);
            const quantity = qtyInput ? qtyInput.value : 1;
            window.location.href = `<?= $basePath ?>/customer/order/now?item=${itemId}&qty=${quantity}`;
        }

        // Modal: Isi form otomatis saat klik Edit
        $('#itemModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var price = button.data('price');
            var desc = button.data('description');
            var stock = button.data('stock');

            var modal = $(this);
            if (id) {
                modal.find('.modal-title').text('Edit Printilan Produk');
                modal.find('#itemForm').attr('action', '<?= $basePath ?>/admin/consumable/product-items/edit/' + id);
                modal.find('#itemId').val(id);
                modal.find('#itemName').val(name);
                modal.find('#itemPrice').val(price);
                modal.find('#itemDescription').val(desc);
                modal.find('#itemStock').val(stock);
            } else {
                modal.find('.modal-title').text('Tambah Printilan Produk');
                modal.find('#itemForm').attr('action', '<?= $basePath ?>/admin/consumable/product-items/create/<?= $productTypeId ?>');
                modal.find('#itemId').val('');
                modal.find('#itemName').val('');
                modal.find('#itemPrice').val('');
                modal.find('#itemDescription').val('');
                modal.find('#itemStock').val('');
            }
        });
    </script>
</body>

</html>