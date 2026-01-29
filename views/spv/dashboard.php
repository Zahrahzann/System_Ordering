<?php
if (!isset($user)) die('Controller tidak menyediakan data user.');
if (!isset($pendingCount)) die('Controller tidak menyediakan data dashboard.');

$basePath = '/system_ordering/public';

if (session_status() === PHP_SESSION_NONE) session_start();

// Ambil notifikasi dari session (pesanan baru waiting approval)
$notif = null;
if (isset($_SESSION['flash_notification'])) {
    $notif = $_SESSION['flash_notification'];
    unset($_SESSION['flash_notification']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/spv/dashboard_spv.css?v=<?= time() ?>" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid dashboard-container">

                    <!-- Welcome Card -->
                    <div class="card welcome-card">
                        <div class="card-body">
                            <div class="welcome-content">
                                <div class="welcome-left">
                                    <h1 class="welcome-title">
                                        <i class="fas fa-hand-sparkles"></i>
                                        Selamat datang, <?= htmlspecialchars($user['name'] ?? '') ?>!
                                    </h1>
                                    <p class="welcome-subtitle">Kelola approval work order dari departemen Anda dengan mudah dan efisien</p>
                                    <div class="user-info-badges">
                                        <div class="user-info-badge">
                                            <i class="fas fa-building"></i>
                                            <span><?= htmlspecialchars($user['department'] ?? '') ?></span>
                                        </div>
                                        <div class="user-info-badge">
                                            <i class="fas fa-industry"></i>
                                            <span><?= htmlspecialchars($user['plant'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="welcome-right">
                                    <div class="profile-avatar">
                                        <?php if (!empty($user['photo'])): ?>
                                            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Profile Photo">
                                        <?php else: ?>
                                            <i class="fas fa-user-circle"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="role-badge">
                                        <i class="fas fa-user-shield mr-1"></i> Supervisor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Cards -->
                    <div class="row stats-row">
                        <!-- Pending -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stat-card pending h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="stat-label">Menunggu Approval</div>
                                            <div class="stat-value"><?= htmlspecialchars($pendingCount ?? 0) ?></div>
                                            <small class="text-muted">Work Order perlu direview</small>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approved -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stat-card approved h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="stat-label">Disetujui</div>
                                            <div class="stat-value"><?= htmlspecialchars($approvedCount ?? 0) ?></div>
                                            <small class="text-muted">Work Order approved</small>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat-icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card stat-card rejected h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="stat-label">Ditolak</div>
                                            <div class="stat-value"><?= htmlspecialchars($rejectedCount ?? 0) ?></div>
                                            <small class="text-muted">Work Order rejected</small>
                                        </div>
                                        <div class="col-auto">
                                            <div class="stat-icon">
                                                <i class="fas fa-times-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card orders-card">
                        <div class="card-header">
                            <h6>
                                <i class="fas fa-list-alt"></i>
                                Work Order Terbaru
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="orders-table table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode Order</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Pemesan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentOrders)): ?>
                                            <?php foreach ($recentOrders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <span class="order-code">
                                                            <i class="fas fa-file-alt mr-1"></i>
                                                            <?= htmlspecialchars($order['code']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="order-date">
                                                            <i class="far fa-calendar mr-1"></i>
                                                            <?= htmlspecialchars($order['date']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = 'badge-secondary';
                                                        $statusText = 'Pending';
                                                        if ($order['status'] == 'approved') {
                                                            $statusClass = 'badge-success';
                                                            $statusText = 'Approved';
                                                        } elseif ($order['status'] == 'rejected') {
                                                            $statusClass = 'badge-danger';
                                                            $statusText = 'Rejected';
                                                        } elseif ($order['status'] == 'pending') {
                                                            $statusClass = 'badge-warning';
                                                            $statusText = 'Pending';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-user mr-1 text-muted"></i>
                                                        <?= htmlspecialchars($order['requested_by']) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4">
                                                    <div class="empty-state">
                                                        <i class="fas fa-inbox"></i>
                                                        <p class="mb-0">Belum ada work order terbaru</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php include __DIR__ . '/../../views/layout/footer.php'; ?>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <!-- SweetAlert Pop-up -->
    <?php if ($notif): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const options = {
                    icon: '<?= htmlspecialchars($notif['type']) ?>',
                    title: '<?= htmlspecialchars($notif['title']) ?>',
                    text: '<?= htmlspecialchars($notif['message']) ?>',
                    showConfirmButton: true,
                    confirmButtonColor: '#667eea'
                };

                <?php if ($notif['type'] === 'success'): ?>
                    options.timer = 3000;
                <?php endif; ?>

                Swal.fire(options);
            });
        </script>
    <?php endif; ?>

</body>

</html>