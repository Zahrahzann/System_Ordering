<?php
// Ambil data dari Controller & Session
if (session_status() === PHP_SESSION_NONE) session_start();
$currentRole = $_SESSION['user_data']['role'] ?? '';
$basePath = '/system_ordering/public';

// Controller mengirim data yang dibutuhkan
if (!isset($order) || !isset($items) || !isset($approval)) {
    die('Controller tidak menyediakan data yang lengkap.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= htmlspecialchars($order['id']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/tracking/detail_wo.css?v=<?= time() ?>" rel="stylesheet">
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
                        <h1 class="page-title">Detail Pesanan</h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                Kelola pesanan customer terbaru, pantau status produksi
                            <?php elseif ($currentRole === 'spv'): ?>
                                Tinjau dan kelola persetujuan pesanan customer dengan efisien
                            <?php else: ?>
                                Pantau status pesanan terbaru Anda, dan lihat detail produksinya
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Informasi Pemesan -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-user"></i> Informasi Pemesan</h6>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Nama Customer</div>
                                    <div class="info-value highlight"><?= htmlspecialchars($order['customer_name']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">NPK</div>
                                    <div class="info-value"><?= htmlspecialchars($order['customer_npk']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Line</div>
                                    <div class="info-value"><?= htmlspecialchars($order['line']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Departemen</div>
                                    <div class="info-value"><?= htmlspecialchars($order['department_name']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Plant</div>
                                    <div class="info-value"><?= htmlspecialchars($order['plant_name']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Tanggal Order</div>
                                    <div class="info-value"><?= date('d F Y H:i', strtotime($order['created_at'])) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Approval SPV -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-clipboard-check"></i> Status Approval SPV</h6>
                        </div>
                        <div class="card-body">
                            <div class="approval-status">
                                <div class="approval-icon approved">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="approval-info">
                                    <div class="approval-spv">
                                        Disetujui oleh:
                                        <strong><?= htmlspecialchars($approval['spv_name'] ?? 'N/A') ?></strong>
                                    </div>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i>
                                        <?= htmlspecialchars(ucfirst($approval['approval_status'] ?? 'Approved')) ?>
                                    </span>
                                </div>
                            </div>
                            <?php if (!empty($approval['comments'])): ?>
                                <div class="comments-box">
                                    <div class="comments-label">Catatan SPV</div>
                                    <div class="comments-text"><?= nl2br(htmlspecialchars($approval['comments'])) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Rincian Item Dipesan -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-boxes"></i> Rincian Item Dipesan (<?= count($items) ?> Item)</h6>
                        </div>
                        <div class="card-body">
                            <div class="items-container">
                                <?php foreach ($items as $index => $item): ?>
                                    <div class="item-card">
                                        <div class="item-header">
                                            <div>
                                                <div style="font-size: 11px; color: #545454ff; margin-bottom: 5px;">ITEM #<?= $index + 1 ?></div>
                                                <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                            </div>
                                            <div>
                                                <?php
                                                if ($item['is_emergency']) {
                                                    echo $item['emergency_type'] === 'line_stop'
                                                        ? '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Line Stop</span>'
                                                        : '<span class="badge badge-success"><i class="fas fa-shield-alt"></i> Safety</span>';
                                                } else {
                                                    echo '<span class="badge badge-info"><i class="fas fa-check"></i> Regular</span>';
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="item-details-grid">
                                            <div class="detail-item">
                                                <div class="detail-label">Kategori</div>
                                                <div class="detail-value"><?= htmlspecialchars($item['category']) ?></div>
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
                                                <div class="detail-label">Estimasi Pengerjaan</div>
                                                <div class="detail-value">
                                                    <?php if (!empty($item['estimasi_pengerjaan'])): ?>
                                                        <i class="fas fa-clock mr-1" style="color:#ff9800;"></i>
                                                        <?= htmlspecialchars($item['estimasi_pengerjaan']) ?>
                                                    <?php else: ?>
                                                        <span style="color:#545454ff;">Belum ditentukan</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Aktual Waktu Pengerjaan</div>
                                                <div class="detail-value">
                                                    <?php
                                                    $minutes = $item['actual_duration_minutes'] ?? null;

                                                    if ($minutes === null || $minutes <= 0) {
                                                        echo '<span style="color:#ff9800;"><i class="fas fa-stopwatch mr-1"></i> Belum dihitung</span>';
                                                    } elseif ($minutes < 60) {
                                                        echo '<i class="fas fa-stopwatch mr-1" style="color:#4caf50;"></i>' . $minutes . ' menit';
                                                    } elseif ($minutes < 1440) {
                                                        echo '<i class="fas fa-stopwatch mr-1" style="color:#4caf50;"></i>' . round($minutes / 60, 1) . ' jam';
                                                    } else {
                                                        echo '<i class="fas fa-stopwatch mr-1" style="color:#4caf50;"></i>' . round($minutes / 1440, 1) . ' hari';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Status Produksi</div>
                                                <div class="detail-value"> <?php $statusLabel = ucwords(str_replace('_', ' ', $item['production_status']));
                                                                            $badgeClass = 'badge-info';
                                                                            if ($item['production_status'] === 'finish') {
                                                                                $badgeClass = 'badge-success';
                                                                            } elseif ($item['production_status'] === 'pending') {
                                                                                $badgeClass = 'badge-secondary';
                                                                            } elseif ($item['production_status'] === 'on_progress') {
                                                                                $badgeClass = 'badge-warning';
                                                                            } ?> <span class="badge <?= $badgeClass ?>"> <?= htmlspecialchars($statusLabel) ?> </span> </div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">PIC MFG</div>
                                                <div class="detail-value">
                                                    <?php if (!empty($item['pic_mfg'])): ?>
                                                        <i class="fas fa-user-tie" style="color: #667eea;"></i>
                                                        <?= htmlspecialchars($item['pic_mfg']) ?>
                                                    <?php else: ?>
                                                        <span style="color: #555555;">Belum ditentukan</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        $files = json_decode($item['file_path'], true);
                                        if (is_array($files) && !empty($files)):
                                        ?>
                                            <div class="files-section">
                                                <div class="files-label">File Drawing</div>
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
                                                        <i class="fas <?= $icon ?>"></i>
                                                        <?= basename($file) ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($item['note'])): ?>
                                            <div class="note-section">
                                                <div class="note-label">Catatan</div>
                                                <div class="note-text"><?= nl2br(htmlspecialchars($item['note'])) ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>
</body>

</html>