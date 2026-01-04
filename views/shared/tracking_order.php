<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? '';

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

$notif = null;
if (isset($_SESSION['flash_notification'])) {
    $notif = $_SESSION['flash_notification'];
    unset($_SESSION['flash_notification']);
}
$role = $_SESSION['user_data']['role'] ?? 'guest';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?= $basePath ?>/assets/css/tracking/tracking_order.css?v=<?= time() ?>" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top" class="tracking-page">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">Tracking Work Order</h1>
                        <p class="page-subtitle">
                            <?php if ($currentRole === 'admin'): ?>
                                Kelola pesanan customer terbaru, pantau status produksi, dan atur PIC MFG dengan mudah
                            <?php elseif ($currentRole === 'spv'): ?>
                                Pantau pesanan departemen terbaru dengan status produksi terkini
                            <?php else: ?>
                                Pantau pesanan terbaru Anda, lihat status produksi, dan detail work order lengkap
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if (empty($items)): ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h4>Belum Ada Work Order</h4>
                            <p>Tidak ada data work order untuk ditampilkan saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php
                        usort($items, function ($a, $b) {
                            $statusOrder = ['finish' => 1, 'on_progress' => 2, 'pending' => 3];
                            $emergencyOrder = ['line_stop' => 1, 'safety' => 2, 'regular' => 3];

                            $statusA = $statusOrder[$a['production_status']] ?? 99;
                            $statusB = $statusOrder[$b['production_status']] ?? 99;

                            if ($statusA !== $statusB) {
                                return $statusA - $statusB;
                            }

                            $typeA = !$a['is_emergency'] ? 'Regular' : ($a['emergency_type'] ?? 'Regular');
                            $typeB = !$b['is_emergency'] ? 'Regular' : ($b['emergency_type'] ?? 'Regular');

                            $emergencyA = $emergencyOrder[$typeA] ?? 99;
                            $emergencyB = $emergencyOrder[$typeB] ?? 99;

                            return $emergencyA - $emergencyB;
                        });
                        ?>
                        <?php $i = 1;
                        foreach ($items as $item): ?>
                            <div class="order-card <?= htmlspecialchars($item['production_status']) ?>">
                                <div class="order-header">
                                    <div class="customer-info">
                                        <div class="order-number">Order</div>
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
                                            <div class="detail-label">Emergency</div>
                                            <div class="detail-value">
                                                <?php
                                                $isEmergency = isset($item['is_emergency']) && (int)$item['is_emergency'] === 1;
                                                $emergencyType = strtolower(trim($item['emergency_type'] ?? 'regular'));

                                                if ($isEmergency && in_array($emergencyType, ['line_stop', 'safety'])) {
                                                    if ($emergencyType === 'line_stop') {
                                                        echo '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Line Stop</span>';
                                                    } else {
                                                        echo '<span class="badge badge-info"><i class="fas fa-shield-alt"></i> Safety</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge badge-success"><i class="fas fa-check"></i> Regular</span>';
                                                }
                                                ?>
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

                                        <!-- Input PIC MFG -->
                                        <input type="text" name="pic_mfg" class="form-control" placeholder="Nama PIC MFG"
                                            value="<?= htmlspecialchars($item['pic_mfg'] ?? '') ?>">

                                        <!-- Input Estimasi Pengerjaan -->
                                        <input type="text" name="estimasi_pengerjaan" class="form-control" placeholder="Estimasi Pengerjaan (contoh: 2 Hari)"
                                            value="<?= htmlspecialchars($item['estimasi_pengerjaan'] ?? '') ?>">

                                        <!-- Status Produksi -->
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
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <?php if ($notif): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let icon = '<?= $notif['type'] ?>';
                let title = '<?= $notif['title'] ?>';
                let text = '<?= $notif['message'] ?>';

                // Sesuaikan pesan berdasarkan role
                <?php if ($role === 'customer'): ?>
                    // Customer: pop‑up status approve/reject
                    Swal.fire({
                        icon,
                        title,
                        text
                    });
                <?php elseif ($role === 'spv'): ?>
                    // SPV: pop‑up hasil approve/reject
                    Swal.fire({
                        icon,
                        title,
                        text
                    });
                <?php elseif ($role === 'admin'): ?>
                    // Admin: pop‑up order baru masuk
                    Swal.fire({
                        icon,
                        title,
                        text
                    });
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>

</body>

</html>