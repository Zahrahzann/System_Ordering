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
    $alerts     = NotificationModel::getUnreadList($department, 'spv', 10);
} elseif ($currentRole === 'customer') {
    $alertCount = NotificationModel::countUnread($userId, 'customer');
    $alerts     = NotificationModel::getUnreadList($userId, 'customer', 10);
} elseif ($currentRole === 'admin') {
    $alertCount = NotificationModel::countUnread($userId, 'admin');
    $alerts     = NotificationModel::getUnreadList($userId, 'admin', 10);
}


// Pesan (sementara kosong dulu kalau belum ada MessageModel)
$messageCount = 0;
$messages     = [];
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

        .rating-stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-stars input[type="radio"]:checked~label,
        .rating-stars label:hover,
        .rating-stars label:hover~label {
            color: gold;
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
                    <!-- Badge counter pakai jumlah unread -->
                    <span class="badge badge-danger badge-counter"><?= $alertCount ?? 0 ?></span>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">

                    <!-- Judul header sesuai role + icon hapus -->
                    <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                        <?php if ($currentRole === 'admin'): ?>
                            Notifikasi Admin
                        <?php elseif ($currentRole === 'spv'): ?>
                            Notifikasi Supervisor
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
                                            // Link sesuai role + type notif
                                            if ($currentRole === 'admin') {
                                                if ($alert['type'] === 'stock_alert') {
                                                    echo $basePath . '/admin/product_items.php';
                                                } else {
                                                    echo $basePath . '/admin/tracking';
                                                }
                                            } elseif ($currentRole === 'spv') {
                                                echo $basePath . '/spv/work_order/approval';
                                            } else {
                                                echo $basePath . '/customer/orders.php';
                                            }
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap JS bundle (required for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="modal fade" id="ratingReviewModal" tabindex="-1" aria-labelledby="ratingReviewLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reviewForm" method="post" action="/customer/review/submit">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingReviewLabel">Isi Rating & Review</h5> <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body"> <!-- order_id akan diisi via JS saat notif diklik -->
                    <input type="hidden" name="customer_id" value="<?= $_SESSION['user_data']['id'] ?>">
                    <input type="hidden" name="order_id" id="orderIdInput" value=""> <label class="form-label">Rating:</label>
                    <div class="rating-stars">
                        <input type="radio" name="rating" id="star5" value="5"><label for="star5">★</label>
                        <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
                        <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
                        <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
                        <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
                    </div>
                    <label for="review" class="form-label">Review:</label>
                    <textarea name="review" id="review" class="form-control" rows="4" placeholder="Tulis ulasanmu di sini..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Kirim</button> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End of Topbar -->

<script>
    // Common function untuk mark notification as read
    function markNotificationRead(notifId) {
        if (!notifId) return Promise.resolve();

        return fetch('/mark_notification.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    notif_id: notifId
                })
            })
            .then(res => res.json())
            .then(result => {
                console.log('Mark read result:', result);
                return result;
            })
            .catch(err => {
                console.error('Mark notification error:', err);
            });
    }

    // Mark All Read Button Handler
    document.addEventListener('DOMContentLoaded', function() {
        const markAllBtn = document.getElementById('markAllRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function() {
                fetch('/system_ordering/public/mark_all_read.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            location.reload();
                        }
                    })
                    .catch(err => console.error('Mark all read error:', err));
            });
        }

        const clearAllBtn = document.getElementById('clearAllNotif');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Hapus Semua Notifikasi?',
                    text: 'Anda yakin ingin menghapus semua notifikasi?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/system_ordering/public/clear_all_notifications.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(result => {
                                if (result.success) {
                                    Swal.fire('Berhasil!', 'Semua notifikasi telah dihapus', 'success')
                                        .then(() => location.reload());
                                }
                            })
                            .catch(err => console.error('Clear all error:', err));
                    }
                });
            });
        }
    });
</script>

<!-- Notif Consumable (Admin Only) -->
<?php if (!empty($_GET['status'])): ?>
    <script>
        (function() {
            const status = "<?= htmlspecialchars($_GET['status']) ?>";
            let config = null;

            switch (status) {
                case 'checkout_success':
                    config = {
                        icon: 'success',
                        title: 'Checkout Berhasil!',
                        text: 'Pesanan dari keranjang berhasil dibuat.',
                        timer: 3000,
                        showConfirmButton: false
                    };
                    break;
                case 'order_success':
                    config = {
                        icon: 'success',
                        title: 'Order Berhasil!',
                        text: 'Pesanan langsung dari katalog berhasil dibuat.',
                        timer: 2000,
                        showConfirmButton: false
                    };
                    break;
                case 'reorder_success':
                    config = {
                        icon: 'success',
                        title: 'Reorder Berhasil!',
                        text: 'Pesanan ulang berhasil dibuat.',
                        timer: 3000,
                        showConfirmButton: false
                    };
                    break;
                case 'shipping':
                    config = {
                        icon: 'info',
                        title: 'Pesanan Sedang Dikirim!',
                        text: 'Barang sedang dalam perjalanan menuju customer.',
                        timer: 3000,
                        showConfirmButton: false
                    };
                    break;
                case 'completed':
                    config = {
                        icon: 'success',
                        title: 'Pesanan Sudah Selesai!',
                        text: 'Barang telah diterima oleh customer.',
                        timer: 3000,
                        showConfirmButton: false
                    };
                    break;
                case 'checkout_failed':
                    config = {
                        icon: 'error',
                        title: 'Checkout Gagal!',
                        text: 'Pesanan tidak bisa diproses.'
                    };
                    break;
            }

            if (config) {
                Swal.fire(config).then(() => {
                    const url = new URL(window.location);
                    url.searchParams.delete('status');
                    window.history.replaceState({}, document.title, url.pathname + url.search);
                });
            }
        })();
    </script>
<?php endif; ?>

<!-- Notifikasi Order Consumable (Custmer Only) -->
<?php if ($currentRole === 'customer'): ?>
    <script>
        (function() {
            let shown = JSON.parse(localStorage.getItem('shown_customer') || '[]');

            function checkCustomerNotifications() {
                fetch('/system_ordering/public/notifications.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.new && !shown.includes(String(data.id))) {
                            if (data.kind === 'shipping') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pesanan Sedang Dikirim!',
                                    text: data.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else if (data.kind === 'completed') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pesanan Sudah Selesai!',
                                    text: data.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                            shown.push(String(data.id));
                            localStorage.setItem('shown_customer', JSON.stringify(shown));
                        }
                    });
            }

            checkCustomerNotifications();
            setInterval(checkCustomerNotifications, 10000);
        })();
    </script>
<?php endif; ?>

<!-- Notifikasi Work Order (Customer Only) -->
<?php if ($currentRole === 'customer'): ?>
    <script>
        (function() {
            // Variabel lokal untuk customer
            let shownNotifications = JSON.parse(localStorage.getItem('shownNotifications_customer') || '[]');

            function checkCustomerNotifications() {
                fetch('/system_ordering/public/notifications.php')
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Customer notif data:', data);

                        // Cek apakah ada notifikasi baru dan belum pernah ditampilkan
                        if (data.new && data.id && !shownNotifications.includes(data.id)) {
                            // Tentukan judul sesuai type
                            let title = 'Notifikasi';
                            if (data.kind === 'order') title = 'Work Order';
                            else if (data.kind === 'approval') title = 'Pesanan Disetujui SPV';

                            Swal.fire({
                                icon: data.type ?? 'info',
                                title: title,
                                html: data.message,
                                confirmButtonText: 'OKE',
                            }).then((result) => {
                                // Mark sebagai sudah dibaca
                                markNotificationRead(data.id);

                                // Simpan ID notifikasi yang sudah ditampilkan
                                shownNotifications.push(data.id);
                                localStorage.setItem('shownNotifications_customer', JSON.stringify(shownNotifications));

                                if (result.isConfirmed && data.kind === 'review') {
                                    document.getElementById('orderIdInput').value = data.order_id || '';
                                    const modalEl = document.getElementById('ratingReviewModal');
                                    const modal = new bootstrap.Modal(modalEl);
                                    modal.show();
                                }
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Customer notif error:', err);
                    });
            }

            // Jalankan pertama kali saat halaman load
            checkCustomerNotifications();

            // Cek setiap 10 detik untuk notifikasi baru
            setInterval(checkCustomerNotifications, 10000);
        })();

        // Submit review via AJAX (tetap di global scope karena butuh akses ke form)
        document.addEventListener('DOMContentLoaded', function() {
            const reviewForm = document.getElementById('reviewForm');
            if (reviewForm) {
                reviewForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch(this.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                const modalEl = document.getElementById('ratingReviewModal');
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terima Kasih!',
                                    text: 'Review Anda telah terkirim. Kami menunggu pesananmu yang lainnya lagi~!'
                                });

                                reviewForm.reset();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: result.message || 'Data review tidak lengkap.'
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Submit review error:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat mengirim review.'
                            });
                        });
                });
            }
        });
    </script>
<?php endif; ?>

<!-- Notifikasi Work Order Approval (SPV Only) -->
<?php if ($currentRole === 'spv'): ?>
    <script>
        (function() {
            // Variabel lokal untuk SPV
            let shownNotifications = JSON.parse(localStorage.getItem('shownNotifications_spv') || '[]');

            function checkSpvNotifications() {
                fetch('/system_ordering/public/notifications.php')
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('SPV notif data:', data);

                        if (data.new && data.id && !shownNotifications.includes(data.id)) {
                            let title = (data.kind === 'approval') ? 'Pengajuan Approval Baru' : 'Notifikasi';

                            Swal.fire({
                                icon: data.type ?? 'info',
                                title: title,
                                html: data.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                markNotificationRead(data.id);

                                shownNotifications.push(data.id);
                                localStorage.setItem('shownNotifications_spv', JSON.stringify(shownNotifications));
                            });
                        }
                    })
                    .catch(err => {
                        console.error('SPV notif error:', err);
                    });
            }

            // Jalankan pertama kali
            checkSpvNotifications();

            // Cek setiap 10 detik
            setInterval(checkSpvNotifications, 10000);
        })();
    </script>
<?php endif; ?>

<!-- Notif stock consumable -->
<?php if ($currentRole === 'admin'): ?>
    <script>
        (function() {
            // Variabel lokal untuk Admin
            let shownNotifications = JSON.parse(localStorage.getItem('shownNotifications_admin') || '[]');

            function checkAdminNotifications() {
                fetch('/system_ordering/public/notifications.php')
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Admin notif data:', data);

                        if (data.new && data.id && !shownNotifications.includes(data.id)) {
                            let title = 'Notifikasi';
                            if (data.kind === 'order') title = 'Pesanan Work Order Baru';
                            else if (data.kind === 'stock_alert') title = 'Stok Produk Rendah';

                            Swal.fire({
                                icon: data.type ?? 'info',
                                title: title,
                                html: data.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                markNotificationRead(data.id);

                                shownNotifications.push(data.id);
                                localStorage.setItem('shownNotifications_admin', JSON.stringify(shownNotifications));
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Admin notif error:', err);
                    });
            }

            function checkLowStock() {
                const lastAlert = localStorage.getItem('lastLowStockAlert');
                const now = Date.now();

                // Cek apakah sudah lewat 1 jam sejak alert terakhir
                if (lastAlert && (now - parseInt(lastAlert, 10)) < 3600000) {
                    console.log("Skip low stock alert, belum 1 jam sejak alert terakhir");
                    return;
                }

                fetch('/system_ordering/public/low_stock.php')
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Low stock data:', data);

                        if (data.alert && data.id && !data.existing) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pengingat Stok Rendah',
                                html: data.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                markNotificationRead(data.id);
                                localStorage.setItem('lastLowStockAlert', now.toString());
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Low stock error:', err);
                    });
            }

            // Jalankan pertama kali saat halaman load
            checkAdminNotifications();
            checkLowStock();

            // Cek notifikasi biasa setiap 10 detik
            setInterval(checkAdminNotifications, 10000);

            // Cek low stock setiap 1 jam
            setInterval(checkLowStock, 3600000);
        })();
    </script>
<?php endif; ?>

<!-- Notif Low Material Stock -->
<?php if ($currentRole === 'admin'): ?>
    <script>
        (function() {
            function checkLowStockMaterial() {
                const lastAlert = localStorage.getItem('lastLowStockMaterialAlert');
                const now = Date.now();

                // skip kalau belum 1 jam sejak alert terakhir
                if (lastAlert && (now - parseInt(lastAlert, 10)) < 3600000) {
                    return;
                }

                fetch('/system_ordering/public/low_stock_material.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.alert) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pengingat Stok Material Rendah',
                                html: data.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                localStorage.setItem('lastLowStockMaterialAlert', now.toString());
                            });
                        }
                    })
                    .catch(err => console.error('Low stock material error:', err));
            }

            // jalankan pertama kali saat halaman load
            checkLowStockMaterial();
            // cek tiap 1 jam (3600000 ms)
            setInterval(checkLowStockMaterial, 3600000);
        })();
    </script>
<?php endif; ?>