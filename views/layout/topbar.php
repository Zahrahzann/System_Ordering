<?php
// $alertCount = NotificationModel::countUnread(); 
// $messageCount = MessageModel::countUnread();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Models\NotificationModel;

$basePath     = '/system_ordering/public';
$userData     = $_SESSION['user_data'] ?? [];
$userId       = $userData['id'] ?? null;
$currentRole  = $userData['role'] ?? 'guest';

$alertCount = 0;
$alerts     = [];

if ($currentRole === 'spv') {
    $department = $userData['department_id'] ?? null;
    $alertCount = NotificationModel::countUnread($department, 'spv');
    $alerts     = NotificationModel::getLatest($department, 'spv');
} elseif ($currentRole === 'customer') {
    $alertCount = NotificationModel::countUnread($userId, 'customer');
    $alerts     = NotificationModel::getLatest($userId, 'customer');
} elseif ($currentRole === 'admin') {
    $alertCount = NotificationModel::countUnread($userId, 'admin');
    $alerts     = NotificationModel::getLatest($userId, 'admin');
}

// Pesan (sementara kosong dulu kalau belum ada MessageModel)
// $messageCount = 0;
// $messages     = [];
?>

<head>
    <style>
        body {
            overflow: hidden;
        }

        #accordionSidebar {
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 1031;
        }

        #content-wrapper {
            padding-left: 224px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #content {
            flex-grow: 1;
            overflow-y: auto;
            padding-top: 88px;
        }

        #content-wrapper .sticky-footer {
            margin-top: auto;
        }

        body.sidebar-toggled #content-wrapper {
            padding-left: 104px;
        }

        @media (max-width: 768px) {
            body.sidebar-toggled #content-wrapper {
                padding-left: 0;
            }
        }
    </style>
</head>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 fixed-top shadow f">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <?php if ($currentRole === 'admin'): ?>
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <span class="badge badge-danger badge-counter"><?= $alertCount ?? 0 ?></span>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">Notifikasi Admin</h6>
                    <?php if (!empty($alerts)): ?>
                        <?php foreach ($alerts as $alert): ?>
                            <a class="dropdown-item d-flex align-items-center"
                                href="<?= $basePath ?>/admin/product_items.php">
                                <div class="mr-3">
                                    <div class="icon-circle bg-<?= $alert['color'] ?? 'warning' ?>">
                                        <i class="<?= $alert['icon'] ?? 'fas fa-exclamation-triangle' ?> text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500"><?= $alert['date'] ?? '-' ?></div>
                                    <span class="font-weight-bold">
                                        <?= $alert['message'] ?? 'Notifikasi sistem' ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dropdown-item text-center small text-gray-500">Belum ada notifikasi</div>
                    <?php endif; ?>
                    <a class="dropdown-item text-center small text-gray-500" href="<?= $basePath ?>/admin/product_items.php">
                        Lihat Semua Produk
                    </a>
                </div>
            </li>
        <?php endif; ?>

        <?php if ($currentRole === 'spv'): ?>
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <span class="badge badge-danger badge-counter"><?= $alertCount ?? 0 ?></span>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">Pesanan Baru</h6>
                    <?php if (!empty($alerts)): ?>
                        <?php foreach ($alerts as $alert): ?>
                            <a class="dropdown-item d-flex align-items-center"
                                href="<?= $basePath ?>/spv/work_order/approval">
                                <div class="mr-3">
                                    <div class="icon-circle bg-<?= $alert['color'] ?? 'warning' ?>">
                                        <i class="<?= $alert['icon'] ?? 'fas fa-exclamation-triangle' ?> text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500"><?= $alert['date'] ?? '-' ?></div>
                                    <span class="font-weight-bold">
                                        <?= $alert['message'] ?? 'Pengajuan Work Order' ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dropdown-item text-center small text-gray-500">Belum ada pesanan baru</div>
                    <?php endif; ?>
                    <a class="dropdown-item text-center small text-gray-500" href="<?= $basePath ?>/spv/work_order/approval">
                        Lihat Semua Pesanan
                    </a>
                </div>
            </li>
        <?php endif; ?>

        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <span class="badge badge-danger badge-counter"><?= $messageCount ?? 0 ?></span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">Message Center</h6>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="dropdown-list-image mr-3">
                                <img class="rounded-circle" src="<?= $msg['avatar'] ?? 'img/default.svg' ?>" alt="...">
                                <div class="status-indicator bg-<?= $msg['status'] ?? 'secondary' ?>"></div>
                            </div>
                            <div class="font-weight-bold">
                                <div class="text-truncate"><?= $msg['text'] ?? 'No message' ?></div>
                                <div class="small text-gray-500"><?= $msg['sender'] ?? 'Unknown' ?> · <?= $msg['time'] ?? '-' ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dropdown-item text-center small text-gray-500">Belum ada pesan</div>
                <?php endif; ?>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION['user_data']['name'] ?? 'Guest') ?>
                </span>
                <img class="img-profile rounded-circle" src="/system_ordering/public/assets/img/undraw_profile.svg" alt="Profile">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/system_ordering/views/customer/logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- End of Topbar -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let lastNotifId = null;

    function checkNotifications() {
        fetch('/system_ordering/public/notifications.php')
            .then(res => res.json())
            .then(data => {
                // Update badge lonceng
                const badge = document.querySelector('#alertsDropdown .badge-counter');
                if (badge) badge.textContent = data.count ?? 0;

                // Debug: lihat isi JSON
                console.log('Notif data:', data);

                // Munculkan pop-up hanya kalau notif baru
                if (data.new && data.id && data.id !== lastNotifId) {
                    lastNotifId = data.id;
                    Swal.fire({
                        icon: data.type,
                        title: 'Pesanan Baru',
                        html: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(err => console.error('Notif error:', err));
    }

    // Cek notif tiap 10 detik
    setInterval(checkNotifications, 10000);
</script>

<script>
    <?php if ($currentRole === 'admin'): ?>

        function checkLowStock() {
            fetch('/system_ordering/public/low_stock.php')
                .then(res => res.json())
                .then(data => {
                    console.log('Low stock data:', data); // debug di console
                    if (data.alert) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pengingat Stok Rendah',
                            html: data.message,
                            confirmButtonText: 'Kelola Stok'
                        }).then(() => {
                            window.location.href = '/system_ordering/views/admin/product-items.php';
                        });
                    }
                })
                .catch(err => console.error('Low stock error:', err));
        }

        // Jalankan saat halaman dibuka
        checkLowStock();

        // Jalankan ulang tiap 1 jam (3600000 ms)
        setInterval(checkLowStock, 3600000); 
    <?php endif; ?>
</script>