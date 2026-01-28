<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$currentRole = $_SESSION['user_data']['role'] ?? '';
$basePath    = '/system_ordering/public';

if (!isset($types) || !isset($dimensions) || !isset($title)) {
    die('Controller tidak menyediakan data yang lengkap.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/manage/materials.css?v=<?= time() ?>" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                Kelola stok material, pantau status ketersediaan, dan atur minimum stok dengan mudah
                            <?php elseif ($currentRole === 'spv'): ?>
                                Pantau status stok material departemen dan kelola ketersediaan dengan mudah
                            <?php else: ?>
                                Lihat status ketersediaan material untuk kebutuhan pemesanan Anda
                            <?php endif; ?>
                        </p>

                        <!-- Search & Action Buttons -->
                        <div class="header-actions">
                            <form id="searchMaterialForm" class="search-form">
                                <input type="text" id="searchMaterialInput" class="form-control" 
                                    placeholder="Cari nama atau nomor material...">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </form>

                            <?php if ($currentRole === 'admin'): ?>
                                <button class="btn btn-primary" id="btnAddType">
                                    <i class="fas fa-plus"></i> Tambah Material
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Flash Notification -->
                    <?php if (!empty($_SESSION['flash_notification'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_notification']['type'] ?>">
                            <i class="fas fa-<?= $_SESSION['flash_notification']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                            <?= htmlspecialchars($_SESSION['flash_notification']['message']) ?>
                        </div>
                        <?php unset($_SESSION['flash_notification']); ?>
                    <?php endif; ?>

                    <!-- Search Result Notification -->
                    <div id="searchNotification" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-info-circle"></i>
                        <span id="searchNotificationText"></span>
                    </div>

                    <!-- Material List -->
                    <?php if (empty($types)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h4>Tidak Ada Data Material</h4>
                            <p>Belum ada material yang terdaftar dalam sistem.</p>
                            <?php if ($currentRole === 'admin'): ?>
                                <p>Silakan tambah jenis material baru untuk memulai.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($types as $t):
                            $dimensionCount = 0;
                            foreach ($dimensions as $d) {
                                if ($d['material_type_id'] == $t['id']) {
                                    $dimensionCount++;
                                }
                            }
                        ?>
                            <div class="card material-card" data-material-id="<?= $t['id'] ?>">
                                <!-- Card Header -->
                                <div class="card-header">
                                    <strong>
                                        <?= htmlspecialchars($t['material_number']) ?> - 
                                        <?= htmlspecialchars($t['name']) ?> 
                                        (<?= $dimensionCount ?> dimensi)
                                    </strong>
                                    
                                    <?php if ($currentRole === 'admin'): ?>
                                        <div>
                                            <button class="btn btn-sm btn-warning btnEditType"
                                                data-id="<?= $t['id'] ?>"
                                                data-number="<?= htmlspecialchars($t['material_number']) ?>"
                                                data-name="<?= htmlspecialchars($t['name']) ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="<?= $basePath ?>/admin/materials/type/delete/<?= $t['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus jenis material ini beserta dimensinya?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Dimensi</th>
                                                <th>Stok Tersedia</th>
                                                <?php if ($currentRole === 'admin'): ?>
                                                    <th>Minimum Stock</th>
                                                    <th>Status</th>
                                                    <th>Riwayat</th>
                                                    <th>Aksi</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dimensions as $d): ?>
                                                <?php if ($d['material_type_id'] == $t['id']):
                                                    $logs = \App\Models\MaterialStockLogModel::getByDimension($d['id']);
                                                    $logCount = count($logs);
                                                    
                                                    $currentStock = (float)$d['stock'];
                                                    $minStock = (float)($d['minimum_stock'] ?? 0);
                                                    $isLowStock = $currentStock <= $minStock;
                                                ?>
                                                    <!-- Main Row -->
                                                    <tr class="<?= $isLowStock && $currentRole === 'admin' ? 'low-stock-row' : '' ?>">
                                                        <td><?= htmlspecialchars($d['dimension']) ?></td>
                                                        <td>
                                                            <span class="stock-badge <?= $isLowStock && $currentRole === 'admin' ? 'stock-low' : 'stock-normal' ?>">
                                                                <?= number_format($currentStock, 0) ?> Unit
                                                            </span>
                                                        </td>

                                                        <?php if ($currentRole === 'admin'): ?>
                                                            <td>
                                                                <span class="minimum-stock-badge">
                                                                    <?= number_format($minStock, 0) ?> Unit
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($isLowStock): ?>
                                                                    <span class="status-badge status-warning">
                                                                        <i class="fas fa-exclamation-triangle"></i> Menipis
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="status-badge status-safe">
                                                                        <i class="fas fa-check-circle"></i> Aman
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($logCount > 0): ?>
                                                                    <button class="btn btn-sm btn-info btnToggleLogs" 
                                                                        data-dimension-id="<?= $d['id'] ?>">
                                                                        <i class="fas fa-history"></i> Lihat (<?= $logCount ?>)
                                                                    </button>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-warning btnEditDimension"
                                                                    data-id="<?= $d['id'] ?>"
                                                                    data-dimension="<?= htmlspecialchars($d['dimension']) ?>"
                                                                    data-stock="<?= $currentStock ?>"
                                                                    data-minimum-stock="<?= $minStock ?>">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <a href="<?= $basePath ?>/admin/materials/dimension/delete/<?= $d['id'] ?>"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Hapus dimensi ini?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>

                                                    <!-- Stock History Row (Hidden by default) -->
                                                    <?php if ($currentRole === 'admin' && $logCount > 0): ?>
                                                        <tr class="stock-history-row" id="logs-<?= $d['id'] ?>" style="display: none;">
                                                            <td colspan="6">
                                                                <div class="stock-history-wrapper">
                                                                    <div class="stock-history-header collapsed">
                                                                        <h6>
                                                                            <i class="fas fa-chevron-down"></i> 
                                                                            Riwayat Stok Material
                                                                        </h6>
                                                                        <span class="text-muted"><?= $logCount ?> transaksi</span>
                                                                    </div>
                                                                    <div class="stock-history-content">
                                                                        <table class="table table-bordered table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Tanggal & Waktu</th>
                                                                                    <th>Jenis Transaksi</th>
                                                                                    <th>Jumlah</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($logs as $log): ?>
                                                                                    <tr>
                                                                                        <td><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></td>
                                                                                        <td>
                                                                                            <?php if ($log['change_type'] === 'IN'): ?>
                                                                                                <span class="text-success">Stok Masuk</span>
                                                                                            <?php else: ?>
                                                                                                <span class="text-danger">Stok Keluar</span>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td><strong><?= number_format($log['quantity'], 0) ?></strong> Unit</td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <?php if ($currentRole === 'admin'): ?>
                                        <!-- Add Dimension Form -->
                                        <form action="<?= $basePath ?>/admin/materials/dimension/store" method="POST" class="add-dimension-form">
                                            <input type="hidden" name="material_type_id" value="<?= $t['id'] ?>">
                                            <input type="text" name="dimension" placeholder="Dimensi (contoh: 10mm x 12m)" required>
                                            <input type="number" step="0.01" name="stock" placeholder="Jumlah stok" min="0" required>
                                            <input type="number" step="0.01" name="minimum_stock" placeholder="Minimum stok" min="0" required>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus-circle"></i> Tambah Dimensi
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Modal: Add/Edit Material Type -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div class="modal fade" id="typeModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="typeForm" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="typeModalTitle">Tambah Jenis Material</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="typeId">
                                            <div class="form-group">
                                                <label>Nomor Material</label>
                                                <input type="text" name="material_number" id="typeNumber" 
                                                    class="form-control" placeholder="Contoh: 9000000" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Material</label>
                                                <input type="text" name="name" id="typeName" 
                                                    class="form-control" placeholder="Contoh: NC BLUE" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal: Edit Dimension -->
                        <div class="modal fade" id="dimensionModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="dimensionForm" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="dimensionModalTitle">Edit Dimensi</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="dimensionId">
                                            <div class="form-group">
                                                <label>Dimensi</label>
                                                <input type="text" name="dimension" id="dimensionValue" 
                                                    class="form-control" placeholder="Contoh: 10mm x 12m" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Stok Tersedia</label>
                                                <input type="number" name="stock" id="dimensionStock" 
                                                    class="form-control" min="0" step="0.01" placeholder="Jumlah unit" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Minimum Stock</label>
                                                <input type="number" name="minimum_stock" id="dimensionMinimumStock"
                                                    class="form-control" min="0" step="0.01" placeholder="Batas minimum stok" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
    <script>
        $(function() {
            // ==================== Search Material ====================
            $('#searchMaterialForm').on('submit', function(e) {
                e.preventDefault();
                const keyword = $('#searchMaterialInput').val().trim().toLowerCase();

                // Hide search notification first
                $('#searchNotification').hide();

                if (keyword.length < 2) {
                    $('#searchNotificationText').text('Masukkan minimal 2 karakter untuk pencarian.');
                    $('#searchNotification').show();
                    return;
                }

                // Simple client-side search
                let visibleCount = 0;
                $('.material-card').each(function() {
                    const text = $(this).text().toLowerCase();
                    const isVisible = text.includes(keyword);
                    $(this).toggle(isVisible);
                    if (isVisible) visibleCount++;
                });

                // Show notification if no results found
                if (visibleCount === 0) {
                    $('#searchNotificationText').text('Material yang Anda cari tidak ditemukan. Coba kata kunci lain.');
                    $('#searchNotification').show();
                    
                    // Auto scroll to notification
                    $('html, body').animate({
                        scrollTop: $('#searchNotification').offset().top - 100
                    }, 400);
                } else {
                    $('#searchNotificationText').text(`Menampilkan ${visibleCount} material yang sesuai dengan pencarian "${keyword}".`);
                    $('#searchNotification').removeClass('alert-warning').addClass('alert-info').show();
                }
            });

            // Clear search on input clear
            $('#searchMaterialInput').on('input', function() {
                if ($(this).val() === '') {
                    $('.material-card').show();
                    $('#searchNotification').hide();
                }
            });

            // ==================== Toggle Stock History ====================
            $('.btnToggleLogs').on('click', function(e) {
                e.stopPropagation();
                const dimensionId = $(this).data('dimension-id');
                const $logsRow = $('#logs-' + dimensionId);
                const $icon = $(this).find('i');

                $logsRow.slideToggle(300);
                
                if ($logsRow.is(':visible')) {
                    $icon.removeClass('fa-history').addClass('fa-times');
                } else {
                    $icon.removeClass('fa-times').addClass('fa-history');
                }
            });

            // ==================== Toggle History Content ====================
            $(document).on('click', '.stock-history-header', function(e) {
                e.stopPropagation();
                const $header = $(this);
                const $content = $header.next('.stock-history-content');
                
                $header.toggleClass('collapsed');
                $content.toggleClass('show');
            });

            // ==================== Modal: Add Material Type ====================
            $('#btnAddType').on('click', function() {
                $('#typeForm')[0].reset();
                $('#typeId').val('');
                $('#typeModalTitle').text('Tambah Jenis Material');
                $('#typeForm').attr('action', '<?= $basePath ?>/admin/materials/type/store');
                $('#typeModal').modal('show');
            });

            // ==================== Modal: Edit Material Type ====================
            $('.btnEditType').on('click', function(e) {
                e.stopPropagation();
                
                const id = $(this).data('id');
                const number = $(this).data('number');
                const name = $(this).data('name');

                $('#typeModalTitle').text('Edit Jenis Material');
                $('#typeForm').attr('action', '<?= $basePath ?>/admin/materials/type/update/' + id);
                $('#typeId').val(id);
                $('#typeNumber').val(number);
                $('#typeName').val(name);

                $('#typeModal').modal('show');
            });

            // ==================== Modal: Edit Dimension ====================
            $('.btnEditDimension').on('click', function(e) {
                e.stopPropagation();

                const id = $(this).data('id');
                const dimension = $(this).data('dimension');
                const stock = $(this).data('stock');
                const minimumStock = $(this).data('minimum-stock');

                $('#dimensionModalTitle').text('Edit Dimensi Material');
                $('#dimensionForm').attr('action', '<?= $basePath ?>/admin/materials/dimension/update/' + id);
                $('#dimensionId').val(id);
                $('#dimensionValue').val(dimension);
                $('#dimensionStock').val(stock);
                $('#dimensionMinimumStock').val(minimumStock);

                $('#dimensionModal').modal('show');
            });
        });
    </script>
</body>

</html>