<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Middleware\SessionMiddleware;
use App\Models\PlantModel;
use App\Models\DepartmentModel;
use App\Models\OrderModel;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

SessionMiddleware::requireCustomerLogin();

$userData       = $_SESSION['user_data'];
$plantName      = PlantModel::getNameById($userData['plant_id']);
$departmentName = DepartmentModel::getNameById($userData['department_id']);

// Ambil flash notification kalau ada
$notif = null;
if (isset($_SESSION['flash_notification'])) {
    $notif = $_SESSION['flash_notification'];
    unset($_SESSION['flash_notification']); 
}
8
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/customer/dashboard.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../views/layout/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column w-100" style="min-height: 100vh;">
            <div id="content">
                <?php include __DIR__ . '/../../views/layout/topbar.php'; ?>

                <div class="container-fluid pt-4">
                    <!-- Welcome Card -->
                    <div class="welcome-card">
                        <div class="welcome-card-content">
                            <div class="welcome-text">
                                <h2>Selamat Datang, Customer <?= htmlspecialchars($userData['name']) ?>! 👋</h2>
                                <div class="welcome-info">
                                    <div class="info-item">
                                        <i class="fas fa-industry"></i>
                                        <span><?= htmlspecialchars($plantName) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-building"></i>
                                        <span><?= htmlspecialchars($departmentName) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-layer-group"></i>
                                        <span><?= htmlspecialchars($userData['line']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="welcome-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?= $stats['total'] ?></div>
                                    <div class="stat-label">Total WO yang Aktif</div>
                                </div>
                                <div class="stat-icon blue">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?= $stats['pending'] ?></div>
                                    <div class="stat-label">WO Pending</div>
                                </div>
                                <div class="stat-icon orange">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?= $stats['on_progress'] ?></div>
                                    <div class="stat-label">WO On Progress</div>
                                </div>
                                <div class="stat-icon yellow">
                                    <i class="fas fa-spinner"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-value"><?= $stats['finish'] ?></div>
                                    <div class="stat-label">WO Done/Finish</div>
                                </div>
                                <div class="stat-icon green">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Section -->
                    <h2 class="section-title">Menu Pengajuan</h2>
                    <div class="menu-grid">
                        <!-- Work Order Card -->
                        <div class="menu-card" onclick="window.location.href='/system_ordering/public/customer/work_order/form'">
                            <div class="menu-card-header">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="menu-card-body">
                                <h3>Work Order</h3>
                                <p>Ajukan permintaan perbaikan atau pemeliharaan peralatan untuk menjaga kelancaran produksi Anda.</p>
                                <div class="menu-card-footer">
                                    <span class="card-action">
                                        Buat Order <i class="fas fa-arrow-right"></i>
                                    </span>
                                    <span class="card-badge">Service</span>
                                </div>
                            </div>
                        </div>

                        <!-- Consumable Card -->
                        <div class="menu-card" onclick="window.location.href='/system_ordering/public/admin/consumable/sections'">
                            <div class="menu-card-header">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="menu-card-body">
                                <h3>Consumable</h3>
                                <p>Permintaan barang habis pakai dan material yang dibutuhkan untuk mendukung proses produksi.</p>
                                <div class="menu-card-footer">
                                    <span class="card-action">
                                        Lihat Katalog <i class="fas fa-arrow-right"></i>
                                    </span>
                                    <span class="card-badge">Products</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include __DIR__ . '/../../views/layout/footer.php'; ?>
        </div>
    </div>

    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>

    <?php if ($notif): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: '<?= $notif['type'] ?>',
                    title: '<?= $notif['title'] ?>',
                    text: '<?= $notif['message'] ?>',
                    showConfirmButton: <?= $notif['type'] === 'success' ? 'false' : 'true' ?>,
                    timer: <?= $notif['type'] === 'success' ? '2000' : 'null' ?>
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>