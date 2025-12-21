<?php

$basePath = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan Work Order</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/shared/history_pesanan.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include __DIR__ . '/../layout/topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="page-header mb-4">
                        <h1 class="page-title">Riwayat Pesanan Work Order</h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                Riwayat Pesanan Work Order seluruh customer
                            <?php elseif ($currentRole === 'spv'): ?>
                                Riwayat Pesanan Work Order dari departemen Anda
                            <?php else: ?>
                                Riwayat Pesanan Anda
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Filter -->
                    <div class="card shadow-sm mb-4 filter-card">
                        <div class="card-body">
                            <form method="GET" class="form-inline">
                                <div class="form-group mr-3 mb-2">
                                    <label class="mr-2">Tahun</label>
                                    <select name="year" class="form-control">
                                        <?php foreach ($availableYears as $y): ?>
                                            <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group mr-3 mb-2">
                                    <label class="mr-2">Bulan</label>
                                    <select name="month" class="form-control">
                                        <option value="">Semua</option>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <?php if ($currentRole === 'admin'): ?>
                                    <div class="form-group mr-3 mb-2">
                                        <label class="mr-2">Departemen</label>
                                        <select name="department" class="form-control">
                                            <option value="">Semua</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>" <?= ($dept['id'] == ($department ?? '')) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <button class="btn btn-primary mb-2">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Riwayat -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="m-0">Daftar Item Selesai</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Item</th>
                                            <th>Qty</th>
                                            <th>Kategori</th>
                                            <th>Jenis Material</th>
                                            <th>PIC MFG</th>
                                            <th>SPV</th>
                                            <th>Tgl Selesai</th>
                                            <th>Catatan</th>
                                            <th>Detail</th>
                                            <?php if ($currentRole === 'customer'): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($items)): ?>
                                            <tr>
                                                <td colspan="<?= $currentRole === 'customer' ? '10' : '9' ?>" class="text-center" style="padding: 3rem;">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="mb-0">Tidak ada riwayat untuk periode ini.</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($item['item_name']) ?></strong></td>
                                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                    <td><?= htmlspecialchars($item['category'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['material_type'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['pic_mfg'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['spv_name'] ?? '-') ?></td>
                                                    <td><?= date('d M Y', strtotime($item['completed_date'])) ?></td>
                                                    <td class="text-center">
                                                        <?php if (!empty($item['note'])): ?>
                                                            <i class="fas fa-comment-dots" title="<?= htmlspecialchars($item['note']) ?>"></i>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal<?= $item['item_id'] ?>">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </button>
                                                    </td>
                                                    <?php if ($currentRole === 'customer'): ?>
                                                        <td>
                                                            <a href="<?= $basePath ?>/customer/history/reorder/<?= $item['item_id'] ?>"
                                                                class="btn btn-success btn-sm"
                                                                onclick="return confirm('Item ini akan dibuka di Form Work Order untuk diedit sebelum dipesan ulang. Lanjutkan?')">
                                                                <i class="fas fa-redo"></i> Pesan Lagi
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail Loop -->
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="modal fade" id="detailModal<?= $item['item_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel<?= $item['item_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-white" id="detailModalLabel<?= $item['item_id'] ?>">
                                                Detail Work Order
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="item-details-grid">
                                                <div class="detail-item">
                                                    <div class="detail-label">Nama Item</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['item_name']) ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Kategori</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['category'] ?? '-') ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Jenis Material</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['material_type'] ?? '-') ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Dimensi Material</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['material_dimension'] ?? '-') ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Quantity</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['quantity']) ?> Unit</div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Tanggal Dibutuhkan</div>
                                                    <div class="detail-value"><?= date('d M Y', strtotime($item['needed_date'])) ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Status Produksi</div>
                                                    <div class="detail-value"><span class="badge badge-info"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $item['production_status']))) ?></span></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">SPV Approval</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['spv_name'] ?? '-') ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Catatan</div>
                                                    <div class="detail-value"><?= htmlspecialchars($item['note'] ?? '-') ?></div>
                                                </div>
                                            </div>

                                            <?php if (!empty($item['file_path'])):
                                                $files = json_decode($item['file_path'], true);
                                                if (is_array($files)): ?>
                                                    <hr>
                                                    <div class="files-section">
                                                        <div class="files-label">File Drawing</div>
                                                        <div>
                                                            <?php foreach ($files as $file):
                                                                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                                $icon = 'fa-file-alt';
                                                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                                    $icon = 'fa-file-image';
                                                                } elseif ($extension === 'pdf') {
                                                                    $icon = 'fa-file-pdf';
                                                                }
                                                            ?>
                                                                <a href="<?= htmlspecialchars($file) ?>" target="_blank" class="file-link">
                                                                    <i class="fas <?= $icon ?>"></i> <?= basename($file) ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                            <?php endif;
                                            endif; ?>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Wrapper -->

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

</body>

</html>