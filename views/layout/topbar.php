<?php
// $alertCount = NotificationModel::countUnread(); 
// $messageCount = MessageModel::countUnread();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Models\NotificationModel;

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

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$customerId = $_SESSION['user_data']['id'] ?? null;

// Notifikasi
$alertCount = NotificationModel::countUnread($customerId);
$alerts     = NotificationModel::getLatest($customerId);

// // Pesan (sementara kosong dulu kalau belum ada MessageModel)
// $messageCount = 0;
// $messages     = [];
?>

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

        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter"><?= $alertCount ?? 0 ?></span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Alerts Center</h6>
                <?php if (!empty($alerts)): ?>
                    <?php foreach ($alerts as $alert): ?>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="mr-3">
                                <div class="icon-circle bg-<?= $alert['color'] ?? 'primary' ?>">
                                    <i class="<?= $alert['icon'] ?? 'fas fa-info-circle' ?> text-white"></i>
                                </div>
                            </div>
                            <!-- <div>
                                <div class="small text-gray-500"><?= $alert['date'] ?? '-' ?></div>
                                <span class="font-weight-bold"><?= $alert['message'] ?? 'No message' ?></span>
                            </div> -->
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dropdown-item text-center small text-gray-500">Belum ada notifikasi</div>
                <?php endif; ?>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
            </div>
        </li>

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