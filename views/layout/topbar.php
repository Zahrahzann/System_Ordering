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

        <!-- NOTIFIKASI -->
        <?php if (in_array($currentRole, ['admin', 'spv', 'customer'])): ?>
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <span class="badge badge-danger badge-counter"><?= $alertCount ?? 0 ?></span>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">

                    <!-- Judul header sesuai role + icon hapus -->
                    <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                        <?php if ($currentRole === 'admin'): ?>
                            Notifikasi Admin
                        <?php elseif ($currentRole === 'spv'): ?>
                            Pesanan Baru
                        <?php else: ?>
                            Notifikasi Customer
                        <?php endif; ?>
                        <i id="clearAllNotif" class="fas fa-trash text-danger ml-2" style="cursor:pointer;"
                            title="Hapus semua notifikasi"></i>
                    </h6>

                    <!-- Isi notifikasi -->
                    <?php if (!empty($alerts)): ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($alerts as $alert): ?>
                                <a class="dropdown-item d-flex align-items-center"
                                    href="<?php
                                            if ($currentRole === 'admin') echo $basePath . '/admin/product_items.php';
                                            elseif ($currentRole === 'spv') echo $basePath . '/spv/work_order/approval';
                                            else echo $basePath . '/customer/orders.php';
                                            ?>">
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
                        </div>
                    <?php else: ?>
                        <div class="dropdown-item text-center small text-gray-500">
                            <?php if ($currentRole === 'admin'): ?>
                                Belum ada notifikasi
                            <?php elseif ($currentRole === 'spv'): ?>
                                Belum ada pesanan baru
                            <?php else: ?>
                                Belum ada notifikasi pesanan
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Footer aksi: tandai semua dibaca -->
                    <div class="dropdown-item text-center">
                        <button id="markAllRead" class="btn btn-sm btn-secondary">
                            <i class="fas fa-check"></i> Tandai Semua Dibaca
                        </button>
                    </div>
                </div>
            </li>
        <?php endif; ?>


        <!-- PESAN/CHAT -->
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

<script>
    // Hapus semua notifikasi (icon trash di header)
    document.getElementById('clearAllNotif')?.addEventListener('click', () => {
        fetch('/system_ordering/public/clear_notifications.php', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let msg = '';
                    if (data.role === 'admin') msg = 'Semua notifikasi stok rendah dihapus';
                    else if (data.role === 'spv') msg = 'Semua pengajuan WO dihapus';
                    else msg = 'Semua notifikasi pesanan dihapus';

                    Swal.fire('Berhasil', msg, 'success');
                    // reset badge & isi dropdown
                    document.querySelector('#alertsDropdown .badge-counter').textContent = '0';
                    document.querySelector('#alertsDropdown .dropdown-list').innerHTML =
                        '<div class="dropdown-item text-center small text-gray-500">Belum ada notifikasi</div>';
                }
            })
            .catch(err => console.error('Clear notif error:', err));
    });

    // Tandai semua notifikasi sebagai dibaca (button di footer)
    document.getElementById('markAllRead')?.addEventListener('click', () => {
        fetch('/system_ordering/public/mark_notifications.php', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let msg = '';
                    if (data.role === 'admin') msg = 'Semua notifikasi stok rendah ditandai sudah dibaca';
                    else if (data.role === 'spv') msg = 'Semua pengajuan WO ditandai sudah dibaca';
                    else msg = 'Semua notifikasi pesanan ditandai sudah dibaca';

                    Swal.fire('Berhasil', msg, 'success');
                    // reset badge counter
                    document.querySelector('#alertsDropdown .badge-counter').textContent = '0';
                }
            })
            .catch(err => console.error('Mark read error:', err));
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($currentRole === 'customer'): ?>
    <script>
        let lastCustomerNotifId = null;

        function checkCustomerNotifications() {
            fetch('/system_ordering/public/notifications.php')
                .then(res => res.json())
                .then(data => {
                    const badge = document.querySelector('#alertsDropdown .badge-counter');
                    if (badge) badge.textContent = data.count ?? 0;

                    console.log('Customer notif data:', data);

                    if (data.new && data.id && data.id !== lastCustomerNotifId) {
                        lastCustomerNotifId = data.id;
                        Swal.fire({
                            icon: data.type ?? 'info',
                            title: 'Status Pesanan',
                            html: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(err => console.error('Customer notif error:', err));
        }

        // Panggil sekali saat halaman dibuka
        checkCustomerNotifications();
    </script>
<?php endif; ?>

<?php if ($currentRole === 'spv'): ?>
    <script>
        let lastNotifId = null;

        function checkNotifications() {
            fetch('/system_ordering/public/notifications.php')
                .then(res => res.json())
                .then(data => {
                    const badge = document.querySelector('#alertsDropdown .badge-counter');
                    if (badge) badge.textContent = data.count ?? 0;

                    console.log('Notif data:', data);

                    if (data.new && data.id && data.id !== lastNotifId) {
                        lastNotifId = data.id;
                        Swal.fire({
                            icon: data.type ?? 'info',
                            title: 'Pengajuan Approval Baru',
                            html: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(err => console.error('Notif error:', err));
        }

        // Jalankan polling hanya kalau masih ada WO waiting
        if (typeof spvPendingCount !== 'undefined' && spvPendingCount > 0) {
            setInterval(checkNotifications, 10000);
        }
    </script>
<?php endif; ?>

<?php if ($currentRole === 'admin'): ?>
    <script>
        function checkLowStock() {
            // Ambil waktu terakhir alert dari localStorage
            const lastAlert = localStorage.getItem('lastLowStockAlert');
            const now = Date.now();

            // Kalau belum 1 jam sejak alert terakhir, skip
            if (lastAlert && (now - parseInt(lastAlert, 10)) < 3600000) {
                console.log("Skip alert, belum 1 jam");
                return;
            }

            fetch('/system_ordering/public/low_stock.php')
                .then(res => res.json())
                .then(data => {
                    console.log('Low stock data:', data);
                    if (data.alert) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pengingat Stok Rendah',
                            html: data.message,
                            confirmButtonText: 'OK'
                        });
                        // Simpan timestamp alert terakhir
                        localStorage.setItem('lastLowStockAlert', now.toString());
                    }
                })
                .catch(err => console.error('Low stock error:', err));
        }

        // Jalankan saat halaman dibuka
        checkLowStock();

        // Jalankan ulang tiap 1 jam
        setInterval(checkLowStock, 3600000);
    </script>
<?php endif; ?>