<?php

$basePath = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Work Order</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/shared/history_pesanan.css?v=<?= time() ?>" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
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
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-history mr-2"></i>Riwayat Pesanan Work Order
                        </h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                <i class="fas fa-info-circle mr-1"></i>Riwayat Pesanan Work Order seluruh customer
                            <?php elseif ($currentRole === 'spv'): ?>
                                <i class="fas fa-info-circle mr-1"></i>Riwayat Pesanan Work Order dari departemen Anda
                            <?php else: ?>
                                <i class="fas fa-info-circle mr-1"></i>Riwayat Pesanan Anda
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Filter -->
                    <div class="card shadow-sm mb-4 filter-card">
                        <div class="card-body">
                            <form method="GET" class="form-inline">
                                <div class="form-group mr-3 mb-2">
                                    <label class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>Tahun</label>
                                    <select name="year" class="form-control">
                                        <?php foreach ($availableYears as $y): ?>
                                            <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group mr-3 mb-2">
                                    <label class="mr-2"><i class="fas fa-calendar mr-1"></i>Bulan</label>
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
                                        <label class="mr-2"><i class="fas fa-building mr-1"></i>Departemen</label>
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
                                    <i class="fas fa-filter mr-1"></i> Filter Data
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Riwayat -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="m-0"><i class="fas fa-list mr-2"></i>Daftar Item Selesai</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <?php if ($currentRole === 'admin'): ?>
                                                <th><i class="fas fa-user mr-1"></i>Customer</th>
                                            <?php endif; ?>
                                            <th><i class="fas fa-box mr-1"></i>Nama Item</th>
                                            <th><i class="fas fa-sort-numeric-up mr-1"></i>Qty</th>
                                            <th><i class="fas fa-tags mr-1"></i>Kategori</th>
                                            <th><i class="fas fa-layer-group mr-1"></i>Material</th>
                                            <th><i class="fas fa-user-tie mr-1"></i>PIC MFG</th>
                                            <th><i class="fas fa-user-shield mr-1"></i>SPV</th>
                                            <th><i class="fas fa-calendar-check mr-1"></i>Selesai</th>
                                            <th class="text-center"><i class="fas fa-info-circle mr-1"></i>Note</th>
                                            <th class="text-center"><i class="fas fa-cog mr-1"></i>Aksi</th>
                                            <?php if ($currentRole === 'customer'): ?>
                                                <th class="text-center"><i class="fas fa-redo mr-1"></i>Reorder</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $colCount = 9;
                                        if ($currentRole === 'admin') $colCount++;
                                        if ($currentRole === 'customer') $colCount++;
                                        ?>
                                        <?php if (empty($items)): ?>
                                            <tr>
                                                <td colspan="<?= $colCount ?>" class="empty-state-cell">
                                                    <i class="fas fa-inbox fa-4x"></i>
                                                    <p class="mb-0">Tidak ada riwayat untuk periode ini.</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <?php if ($currentRole === 'admin'): ?>
                                                        <td><strong><?= htmlspecialchars($item['customer_name'] ?? '-') ?></strong></td>
                                                    <?php endif; ?>
                                                    <td><strong><?= htmlspecialchars($item['item_name'] ?? '-') ?></strong></td>
                                                    <td><?= htmlspecialchars($item['quantity'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['category'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['material_type'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['pic_mfg'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['spv_name'] ?? '-') ?></td>
                                                    <td>
                                                        <?= !empty($item['completed_date'])
                                                            ? date('d M Y', strtotime($item['completed_date']))
                                                            : '-' ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($item['note'])): ?>
                                                            <i class="fas fa-comment-dots text-info" style="cursor: pointer;" title="<?= htmlspecialchars($item['note']) ?>"></i>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal<?= $item['item_id'] ?? '' ?>">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </button>
                                                    </td>
                                                    <?php if ($currentRole === 'customer'): ?>
                                                        <td class="text-center">
                                                            <a href="<?= $basePath ?>/customer/history/reorder/<?= $item['item_id'] ?? '' ?>"
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
                            <div class="modal fade" id="detailModal<?= $item['item_id'] ?? '' ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-white">
                                                <i class="fas fa-file-alt mr-2"></i>Detail Work Order - <?= htmlspecialchars($item['item_name'] ?? '') ?>
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <!-- Tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#detailTab<?= $item['item_id'] ?? '' ?>">
                                                        <i class="fas fa-info-circle mr-1"></i>Detail WO
                                                    </a>
                                                </li>
                                                <?php if ($currentRole === 'admin' && ($item['production_status'] ?? '') === 'completed'): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-toggle="tab" href="#costTab<?= $item['item_id'] ?? '' ?>">
                                                            <i class="fas fa-dollar-sign mr-1"></i>Input Data Laporan
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>

                                            <!-- Tab Content -->
                                            <div class="tab-content">
                                                <!-- Detail WO Tab -->
                                                <div class="tab-pane fade show active" id="detailTab<?= $item['item_id'] ?? '' ?>">
                                                    <div class="item-details-grid">
                                                        <?php if ($currentRole === 'admin'): ?>
                                                            <div class="detail-item">
                                                                <div class="detail-label"><i class="fas fa-user mr-1"></i>Nama Customer</div>
                                                                <div class="detail-value"><?= htmlspecialchars($item['customer_name'] ?? '-') ?></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-box mr-1"></i>Nama Item</div>
                                                            <div class="detail-value"><?= htmlspecialchars($item['item_name'] ?? '-') ?></div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-tags mr-1"></i>Kategori</div>
                                                            <div class="detail-value"><?= htmlspecialchars($item['category'] ?? '-') ?></div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-layer-group mr-1"></i>Jenis Material</div>
                                                            <div class="detail-value"><?= htmlspecialchars($item['material_type'] ?? '-') ?></div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-ruler mr-1"></i>Dimensi Material</div>
                                                            <div class="detail-value"><?= htmlspecialchars($item['material_dimension'] ?? '-') ?></div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-sort-numeric-up mr-1"></i>Quantity</div>
                                                            <div class="detail-value"><?= htmlspecialchars($item['quantity'] ?? '-') ?> Unit</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-calendar-alt mr-1"></i>Tanggal Dibutuhkan</div>
                                                            <div class="detail-value">
                                                                <?= !empty($item['needed_date']) ? date('d M Y', strtotime($item['needed_date'])) : '-' ?>
                                                            </div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-tasks mr-1"></i>Status Produksi</div>
                                                            <div class="detail-value">
                                                                <span class="badge badge-info">
                                                                    <?= !empty($item['production_status'])
                                                                        ? htmlspecialchars(ucwords(str_replace('_', ' ', $item['production_status'])))
                                                                        : '-' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label"><i class="fas fa-user-shield mr-1"></i>SPV Approval</div>
                                                            <div class="detail-value"><?= htmlspecialchars($item['spv_name'] ?? '-') ?></div>
                                                        </div>
                                                    </div>

                                                    <?php if (!empty($item['file_path'])):
                                                        $files = json_decode($item['file_path'], true);
                                                        if (is_array($files)): ?>
                                                            <div class="files-section">
                                                                <div class="files-label"><i class="fas fa-file-image mr-2"></i>File Drawing</div>
                                                                <div>
                                                                    <?php foreach ($files as $file):
                                                                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                                        $icon = 'fa-file-alt';
                                                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = 'fa-file-image';
                                                                        elseif ($extension === 'pdf') $icon = 'fa-file-pdf';
                                                                    ?>
                                                                        <a href="<?= htmlspecialchars($file) ?>" target="_blank" class="file-link">
                                                                            <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars(basename($file)) ?>
                                                                        </a>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                    <?php endif;
                                                    endif; ?>
                                                </div>

                                                <!-- Cost Input Tab (Admin Only) -->
                                                <?php if ($currentRole === 'admin' && ($item['production_status'] ?? '') === 'completed'): ?>
                                                    <div class="tab-pane fade" id="costTab<?= htmlspecialchars($item['item_id'] ?? '') ?>">
                                                        <div class="cost-form-wrapper">
                                                            <h5 class="cost-form-title">
                                                                <i class="fas fa-calculator mr-2"></i> Input Biaya Work Order
                                                            </h5>

                                                            <form method="POST" action="/system_ordering/public/admin/workorder/savecost">
                                                                <!-- Hidden Fields -->
                                                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($item['order_id'] ?? '') ?>">
                                                                <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['item_name'] ?? '') ?>">
                                                                <input type="hidden" name="department_id" value="<?= htmlspecialchars($item['department_id'] ?? ($_SESSION['user_data']['department_id'] ?? '')) ?>">
                                                                <input type="hidden" name="customer_id" value="<?= htmlspecialchars($item['customer_id'] ?? ($_SESSION['user_data']['customer_id'] ?? '')) ?>">
                                                                <input type="hidden" name="status" value="<?= htmlspecialchars($item['production_status'] ?? '') ?>">

                                                                <!-- Material Cost -->
                                                                <div class="form-group">
                                                                    <label><i class="fas fa-boxes mr-1"></i> Material Cost (Rp)</label>
                                                                    <input type="number" step="0.01" name="material_cost" class="form-control" min="0"
                                                                        placeholder="Masukkan biaya material" required>
                                                                </div>

                                                                <!-- Machine Section (multi process) -->
                                                                <div class="machine-section">
                                                                    <h6><i class="fas fa-cogs mr-1"></i> Machine Process</h6>

                                                                    <!-- Container untuk semua baris proses mesin -->
                                                                    <div id="machineRows">
                                                                        <!-- Baris pertama -->
                                                                        <div class="form-row-grid">
                                                                            <div class="form-group">
                                                                                <label>Proses Mesin</label>
                                                                                <select name="machine_process[]" class="form-control" required>
                                                                                    <option value="">Pilih Proses Mesin</option>
                                                                                    <?php if (!empty($machineRates)): ?>
                                                                                        <?php foreach ($machineRates as $rate): ?>
                                                                                            <option value="<?= htmlspecialchars($rate['process_name']) ?>">
                                                                                                <?= htmlspecialchars($rate['process_name']) ?>
                                                                                                (Rp <?= number_format($rate['price_per_minute'], 0, ',', '.') ?>/menit)
                                                                                            </option>
                                                                                        <?php endforeach; ?>
                                                                                    <?php else: ?>
                                                                                        <option value="">Data rate mesin tidak tersedia</option>
                                                                                    <?php endif; ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Durasi (menit)</label>
                                                                                <input type="number" name="machine_time[]" class="form-control" min="0"
                                                                                    placeholder="Durasi dalam menit" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Tombol tambah baris -->
                                                                    <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addMachineRow()">
                                                                        <i class="fas fa-plus mr-1"></i> Tambah Proses Mesin
                                                                    </button>
                                                                </div>

                                                                <!-- Manpower Section (multi process) -->
                                                                <div class="manpower-section mt-3">
                                                                    <h6><i class="fas fa-users mr-1"></i> Manpower Process</h6>

                                                                    <!-- Container untuk semua baris manpower -->
                                                                    <div id="manpowerRows">
                                                                        <!-- Baris pertama -->
                                                                        <div class="form-row-grid">
                                                                            <div class="form-group">
                                                                                <label>Proses Tenaga Kerja</label>
                                                                                <select name="manpower_process[]" class="form-control" required>
                                                                                    <option value="">Pilih Proses Tenaga Kerja</option>
                                                                                    <?php if (!empty($manpowerRates)): ?>
                                                                                        <?php foreach ($manpowerRates as $rate): ?>
                                                                                            <option value="<?= htmlspecialchars($rate['process_name']) ?>">
                                                                                                <?= htmlspecialchars($rate['process_name']) ?>
                                                                                                (Rp <?= number_format($rate['price_per_minute'], 0, ',', '.') ?>/menit)
                                                                                            </option>
                                                                                        <?php endforeach; ?>
                                                                                    <?php else: ?>
                                                                                        <option value="">Data rate tenaga kerja tidak tersedia</option>
                                                                                    <?php endif; ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Durasi (menit)</label>
                                                                                <input type="number" name="manpower_time[]" class="form-control" min="0"
                                                                                    placeholder="Durasi dalam menit" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Tombol tambah baris -->
                                                                    <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addManpowerRow()">
                                                                        <i class="fas fa-plus mr-1"></i> Tambah Proses Tenaga Kerja
                                                                    </button>
                                                                </div>

                                                                <!-- Vendor Price -->
                                                                <div class="form-group mt-3">
                                                                    <label><i class="fas fa-handshake mr-1"></i> Vendor Price per pcs (Rp)</label>
                                                                    <input type="number" step="0.01" name="vendor_price_per_pcs" class="form-control" min="0"
                                                                        placeholder="Masukkan harga vendor per unit" required>
                                                                </div>

                                                                <div class="text-right mt-4">
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="fas fa-save mr-2"></i> Simpan Data Biaya
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
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

    <script>
        function addMachineRow() {
            const container = document.getElementById('machineRows');
            const firstRow = container.querySelector('.form-row-grid');
            if (!firstRow) {
                console.error("Baris pertama machine tidak ditemukan");
                return;
            }

            // Clone baris pertama
            const newRow = firstRow.cloneNode(true);

            // Reset value select & input
            const select = newRow.querySelector('select');
            const input = newRow.querySelector('input');
            if (select) select.selectedIndex = 0;
            if (input) input.value = '';

            // Tambahkan tombol hapus jika belum ada
            let removeBtn = newRow.querySelector('.remove-row');
            if (!removeBtn) {
                removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger remove-row ml-2';
                removeBtn.textContent = 'Hapus';
                removeBtn.addEventListener('click', () => newRow.remove());
                newRow.appendChild(removeBtn);
            }

            container.appendChild(newRow);
        }

        function addManpowerRow() {
            const container = document.getElementById('manpowerRows');
            const firstRow = container.querySelector('.form-row-grid');
            if (!firstRow) {
                console.error("Baris pertama manpower tidak ditemukan");
                return;
            }

            // Clone baris pertama
            const newRow = firstRow.cloneNode(true);

            // Reset value select & input
            const select = newRow.querySelector('select');
            const input = newRow.querySelector('input');
            if (select) select.selectedIndex = 0;
            if (input) input.value = '';

            // Tambahkan tombol hapus jika belum ada
            let removeBtn = newRow.querySelector('.remove-row');
            if (!removeBtn) {
                removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger remove-row ml-2';
                removeBtn.textContent = 'Hapus';
                removeBtn.addEventListener('click', () => newRow.remove());
                newRow.appendChild(removeBtn);
            }

            container.appendChild(newRow);
        }

        // Pasang listener hapus untuk baris pertama juga
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.remove-row').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.target.closest('.form-row-grid').remove();
                });
            });
        });
    </script>

</body>

</html>