<?php
if (!isset($user)) die('Controller tidak menyediakan data user.');
if (!isset($pendingCount)) die('Controller tidak menyediakan data dashboard.');
$basePath = '/system_ordering/public';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>
                <div class="container-fluid">
                    
                    <!-- Welcome Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-tie"></i> Supervisor Dashboard
                            </h6>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-2">Selamat datang, <span class="text-primary"><?= htmlspecialchars($user['name'] ?? '') ?></span></h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-building text-primary"></i> Department: <strong><?= htmlspecialchars($user['department'] ?? '') ?></strong> | 
                                <i class="fas fa-industry text-primary"></i> Plant: <strong><?= htmlspecialchars($user['plant'] ?? '') ?></strong>
                            </p>
                        </div>
                    </div>

                    <!-- Statistik Cards -->
                    <div class="row">
                        <!-- Pending -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Menunggu Approval
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($pendingCount ?? 0) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approved -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Approved
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($approvedCount ?? 0) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Rejected
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($rejectedCount ?? 0) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Terbaru -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list"></i> Order Terbaru
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
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
                                                    <td><?= htmlspecialchars($order['code']) ?></td>
                                                    <td><?= htmlspecialchars($order['date']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = 'badge-secondary';
                                                        if ($order['status'] == 'approved') $statusClass = 'badge-success';
                                                        elseif ($order['status'] == 'rejected') $statusClass = 'badge-danger';
                                                        elseif ($order['status'] == 'pending') $statusClass = 'badge-warning';
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                                                    </td>
                                                    <td><?= htmlspecialchars($order['requested_by']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p class="mb-0">Belum ada order terbaru</p>
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
</body>

</html>