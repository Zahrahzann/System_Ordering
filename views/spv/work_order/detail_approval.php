<?php
// Data $order dan $items dikirim dari Controller
if (!isset($order) || !isset($items)) die('Controller tidak menyediakan data.');
$basePath = '/system_ordering/public';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Approval Order #<?= htmlspecialchars($order['id']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/spv/work_order/detail_approval.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../../views/layout/topbar.php'; ?>
                <div class="container-fluid">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-clipboard-check"></i>
                            Detail Approval Order <span class="order-number">#<?= htmlspecialchars($order['id']) ?></span>
                        </h1>
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

                    <!-- Item yang Dipesan -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-boxes"></i> Item yang Dipesan (<?= count($items) ?> Item)</h6>
                        </div>
                        <div class="card-body">
                            <div class="items-container">
                                <?php foreach ($items as $index => $item): ?>
                                    <div class="item-card">
                                        <div class="item-header">
                                            <div>
                                                <div class="item-number">ITEM #<?= $index + 1 ?></div>
                                                <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                            </div>
                                            <div>
                                                <?php
                                                if ($item['is_emergency']) {
                                                    echo $item['emergency_type'] === 'line_stop'
                                                        ? '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Line Stop</span>'
                                                        : '<span class="badge badge-info"><i class="fas fa-shield-alt"></i> Safety</span>';
                                                } else {
                                                    echo '<span class="badge badge-success"><i class="fas fa-check"></i> Regular</span>';
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
                                                <div class="detail-label">Quantity</div>
                                                <div class="detail-value"><?= htmlspecialchars($item['quantity']) ?> Unit</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Material</div>
                                                <div class="detail-value"><?= htmlspecialchars($item['material']) ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Jenis Material</div>
                                                <div class="detail-value"><?= htmlspecialchars($item['material_type']) ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Tanggal Dibutuhkan</div>
                                                <div class="detail-value">
                                                    <i class="far fa-calendar" style="color: #667eea;"></i>
                                                    <?= date('d M Y', strtotime($item['needed_date'])) ?>
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

                    <!-- Tindakan Approval -->
                    <div class="card approval-card">
                        <div class="card-header">
                            <h6><i class="fas fa-tasks"></i> Tindakan Approval</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= $basePath ?>/spv/work_order/process_approval/<?= $order['id'] ?>" method="POST">
                                <div class="form-group">
                                    <label for="spv_notes">Catatan Tambahan (Opsional)</label>
                                    <textarea name="spv_notes" id="spv_notes" class="form-control" rows="3" placeholder="Contoh: Mohon segera diproses atau berikan alasan reject..."></textarea>
                                </div>
                                <div class="action-buttons">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve Order
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Reject Order
                                    </button>
                                    <a href="<?= $basePath ?>/spv/work_order/approval" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php include __DIR__ . '/../../../views/layout/footer.php'; ?>
        </div>
    </div>
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>