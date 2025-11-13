<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? '';

// Fungsi helper untuk badge status
function getStatusBadge($status)
{
    switch ($status) {
        case 'pending':
            return 'badge-secondary';
        case 'on_progress':
            return 'badge-warning';
        case 'finish':
            return 'badge-success';
        case 'completed':
            return 'badge-info';
        case 'rejected':
            return 'badge-danger';
        default:
            return 'badge-light text-dark';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/tracking/tracking_order.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-tasks"></i>
                            Tracking Work Order
                        </h1>
                    </div>

                    <?php if (empty($items)): ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h4>Belum Ada Work Order</h4>
                            <p>Tidak ada data work order untuk ditampilkan saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php $i = 1;
                        foreach ($items as $item): ?>
                            <div class="order-card <?= htmlspecialchars($item['production_status']) ?>">
                                <div class="order-header">
                                    <div class="customer-info">
                                        <div class="order-number">Order #<?= htmlspecialchars($item['order_id']) ?></div>
                                        <h5><?= htmlspecialchars($item['customer_name']) ?></h5>
                                        <div class="customer-line">
                                            <i class="fas fa-layer-group"></i>
                                            Line: <?= htmlspecialchars($item['line']) ?>
                                        </div>
                                    </div>
                                    <div class="status-badge-wrapper">
                                        <div class="status-label">Status</div>
                                        <span class="badge <?= getStatusBadge($item['production_status']) ?>">
                                            <?php
                                            $statusIcons = [
                                                'pending' => 'fa-clock',
                                                'on_progress' => 'fa-spinner',
                                                'finish' => 'fa-check-circle',
                                                'completed' => 'fa-check-double'
                                            ];
                                            $icon = $statusIcons[$item['production_status']] ?? 'fa-info-circle';
                                            ?>
                                            <i class="fas <?= $icon ?> mr-1"></i>
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $item['production_status']))) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="order-content">
                                    <div class="order-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Work Order</div>
                                            <div class="detail-value highlight"><?= htmlspecialchars($item['item_name']) ?></div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Quantity</div>
                                            <div class="detail-value"><?= htmlspecialchars($item['quantity']) ?> Unit</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">PIC MFG</div>
                                            <div class="detail-value">
                                                <?php if (!empty($item['pic_mfg'])): ?>
                                                    <i class="fas fa-user-tie mr-1" style="color: #667eea;"></i>
                                                    <?= htmlspecialchars($item['pic_mfg']) ?>
                                                <?php else: ?>
                                                    <span style="color: #545454ff;">Belum ditentukan</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-actions">
                                        <?php
                                        $detailUrl = "{$basePath}/" . htmlspecialchars($currentRole) . "/tracking/detail/" . $item['order_id'];
                                        ?>
                                        <a href="<?= $detailUrl ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail WO
                                        </a>
                                    </div>
                                </div>

                                <?php if ($currentRole === 'admin'): ?>
                                    <form method="POST" action="<?= $basePath ?>/admin/tracking/update_item/<?= $item['item_id'] ?>" class="update-form">
                                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">

                                        <input type="text" name="pic_mfg" class="form-control" placeholder="Nama PIC MFG"
                                            value="<?= htmlspecialchars($item['pic_mfg'] ?? '') ?>">

                                        <select name="status" class="form-control">
                                            <option value="pending" <?= $item['production_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="on_progress" <?= $item['production_status'] == 'on_progress' ? 'selected' : '' ?>>On Progress</option>
                                            <option value="finish" <?= $item['production_status'] == 'finish' ? 'selected' : '' ?>>Finish</option>
                                            <option value="completed" <?= $item['production_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>

                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                    </form>

                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php include __DIR__ . '/../../views/layout/footer.php'; ?>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

</body>

</html>