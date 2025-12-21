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
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/manage/materials.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h1 class="page-title"><i class="fas fa-cubes"></i> <?= htmlspecialchars($title) ?></h1>
                        <?php if ($currentRole === 'admin'): ?>
                            <button class="btn btn-primary" id="btnAddType">
                                <i class="fas fa-plus"></i> Tambah Jenis Material
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Flash Notification -->
                    <?php if (!empty($_SESSION['flash_notification'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_notification']['type'] ?>">
                            <?= htmlspecialchars($_SESSION['flash_notification']['message']) ?>
                        </div>
                        <?php unset($_SESSION['flash_notification']); ?>
                    <?php endif; ?>

                    <?php if (empty($types)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h4>Tidak Ada Data Material</h4>
                            <p>Belum ada material yang terdaftar.</p>
                            <?php if ($currentRole === 'admin'): ?>
                                <p>Silakan tambah jenis material baru.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($types as $t): ?>
                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong><?= htmlspecialchars($t['material_number']) ?> - <?= htmlspecialchars($t['name']) ?></strong>
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
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Dimension</th>
                                                <th>Stock</th>
                                                <?php if ($currentRole === 'admin'): ?>
                                                    <th>Aksi</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dimensions as $d): ?>
                                                <?php if ($d['material_type_id'] == $t['id']): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($d['dimension']) ?></td>
                                                        <td><?= (int)$d['stock'] ?> Unit</td>
                                                        <?php if ($currentRole === 'admin'): ?>
                                                            <td>
                                                                <button class="btn btn-sm btn-warning btnEditDimension"
                                                                    data-id="<?= $d['id'] ?>"
                                                                    data-dimension="<?= htmlspecialchars($d['dimension']) ?>"
                                                                    data-stock="<?= (int)$d['stock'] ?>">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                                <a href="<?= $basePath ?>/admin/materials/dimension/delete/<?= $d['id'] ?>"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Hapus dimensi ini?')">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </a>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <?php if ($currentRole === 'admin'): ?>
                                        <!-- Form tambah dimension -->
                                        <form action="<?= $basePath ?>/admin/materials/dimension/store" method="POST" class="mt-2">
                                            <input type="hidden" name="material_type_id" value="<?= $t['id'] ?>">
                                            <input type="text" name="dimension" placeholder="Dimension" required>
                                            <input type="number" step="0.01" name="stock" placeholder="Stock" required>
                                            <button type="submit" class="btn btn-sm btn-primary">Tambah Dimensi</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Modal Form Type (hanya admin) -->
                    <?php if ($currentRole === 'admin'): ?>
                        <div class="modal fade" id="typeModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="typeForm" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="typeModalTitle">Tambah Jenis Material</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="typeId">
                                            <div class="form-group">
                                                <label>Nomor Material</label>
                                                <input type="text" name="material_number" id="typeNumber" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Material</label>
                                                <input type="text" name="name" id="typeName" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Form Dimension -->
                        <div class="modal fade" id="dimensionModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="dimensionForm" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="dimensionModalTitle">Edit Dimensi</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="dimensionId">
                                            <div class="form-group">
                                                <label>Dimensi</label>
                                                <input type="text" name="dimension" id="dimensionValue" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Stock</label>
                                                <input type="number" name="stock" id="dimensionStock" class="form-control" min="0" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
                    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
                    <script>
                        $(function() {
                            // ==================== Setup Initial State ====================

                            $('.card').each(function() {
                                const $card = $(this);
                                const dimensionCount = $card.find('tbody tr').length;

                                // Tambahkan jumlah dimensi ke teks header
                                const $headerText = $card.find('.card-header strong');
                                const originalText = $headerText.text().trim();
                                $headerText.text(originalText + ' (' + dimensionCount + ' dimensi)');
                            });

                            $('.card-body').removeClass('show');
                            $('.card-header').addClass('collapsed');
                            $('.card').removeClass('active');

                            // ==================== Accordion Functionality ====================

                            $('.card-header').on('click', function(e) {
                                if ($(e.target).closest('.btn-sm, .btn-warning, .btn-danger').length > 0) return;

                                const $header = $(this);
                                const $card = $header.closest('.card');
                                const $body = $header.next('.card-body');
                                const isCollapsed = $header.hasClass('collapsed');

                                if (isCollapsed) {
                                    $header.removeClass('collapsed');
                                    $body.addClass('show');
                                    $card.addClass('active');
                                } else {
                                    $header.addClass('collapsed');
                                    $body.removeClass('show');
                                    $card.removeClass('active');
                                }

                                // Optional strict accordion mode
                                /*
                                if (isCollapsed) {
                                    $('.card').not($card).removeClass('active');
                                    $('.card-header').not($header).addClass('collapsed');
                                    $('.card-body').not($body).removeClass('show');
                                }
                                */
                            });

                            // ==================== Modal Type ====================

                            $('#btnAddType').on('click', function() {
                                $('#typeForm')[0].reset();
                                $('#typeId').val('');
                                $('#typeModalTitle').text('Tambah Jenis Material');
                                $('#typeForm').attr('action', '<?= $basePath ?>/admin/materials/type/store');
                                $('#typeModal').modal('show');
                            });

                            $('.btnEditType').on('click', function(e) {
                                e.stopPropagation();

                                $('#typeForm')[0].reset();

                                const id = $(this).data('id');
                                const number = $(this).data('number');
                                const name = $(this).data('name');

                                $('#typeModalTitle').text('Edit Jenis Material');
                                $('#typeForm').attr('action', '<?= $basePath ?>/admin/materials/type/update/' + encodeURIComponent(id));
                                $('#typeId').val(id);
                                $('#typeNumber').val(number);
                                $('#typeName').val(name);

                                $('#typeModal').modal('show');
                            });

                            // ==================== Modal Dimension ====================

                            $('.btnEditDimension').on('click', function(e) {
                                e.stopPropagation();

                                $('#dimensionForm')[0].reset();

                                const id = $(this).data('id');
                                const dimension = $(this).data('dimension');
                                const stock = $(this).data('stock');

                                $('#dimensionModalTitle').text('Edit Dimensi');
                                $('#dimensionForm').attr('action', '<?= $basePath ?>/admin/materials/dimension/update/' + encodeURIComponent(id));
                                $('#dimensionId').val(id);
                                $('#dimensionValue').val(dimension);
                                $('#dimensionStock').val(stock);

                                $('#dimensionModal').modal('show');
                            });

                            // ==================== Prevent Event Bubbling ====================

                            $('.btn-sm, .btn-warning, .btn-danger, a[href*="delete"]').on('click', function(e) {
                                e.stopPropagation();
                            });

                            $('.card-body form, .card-body form input').on('click', function(e) {
                                e.stopPropagation();
                            });

                            // ==================== Smooth Scroll to Opened Card ====================

                            $('.card-header').on('click', function() {
                                const $header = $(this);
                                setTimeout(function() {
                                    if (!$header.hasClass('collapsed')) {
                                        $('html, body').animate({
                                            scrollTop: $header.offset().top - 100
                                        }, 400);
                                    }
                                }, 100);
                            });

                            // ==================== Auto-open first card (optional) ====================
                            /*
                            if ($('.card').length > 0) {
                                $('.card').first().find('.card-header').trigger('click');
                            }
                            */
                        });
                    </script>

</body>

</html>