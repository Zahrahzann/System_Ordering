<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentRole = $_SESSION['user_data']['role'] ?? '';

$basePath = '/system_ordering/public';
$dashboardPath = '#'; 
if ($currentRole === 'customer') {
    $dashboardPath = $basePath . '/customer/dashboard';
} elseif ($currentRole === 'spv') {
    $dashboardPath = $basePath . '/spv/dashboard';
} elseif ($currentRole === 'admin') {
    $dashboardPath = $basePath . '/admin/dashboard';
}
?>

<head>
    <style>
        /* Hanya sembunyikan scroll horizontal agar content tidak hilang */
        body {
            overflow-x: hidden;
        }

        #accordionSidebar {
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 1031;
            overflow-y: auto;
            overflow-x: hidden;
        }

        #content-wrapper {
            /* gunakan padding-left mengikuti default sb-admin layout */
            padding-left: 224px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #content {
            flex-grow: 1;
            overflow-y: auto;
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
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $dashboardPath ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cog"></i>
        </div>
        <div class="sidebar-brand-text mx-3"><?= ucfirst(htmlspecialchars($currentRole)) ?><sup>ME</sup></div>
    </a>
    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?= $dashboardPath ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- SIDEBAR UNTUK CUSTOMER -->
    <?php if ($currentRole === 'customer') : ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Work Order</div>
        <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>/customer/work_order/form">
                <i class="fas fa-fw fa-edit"></i>
                <span>Form Work Order</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomerWO">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Work Order Menu</span>
            </a>
            <div id="collapseCustomerWO" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Menu</h6>
                    <a class="collapse-item" href="<?= $basePath ?>/customer/cart">Cart</a>
                    <a class="collapse-item" href="<?= $basePath ?>/customer/checkout">Checkout</a>
                    <a class="collapse-item" href="<?= $basePath ?>/customer/tracking">Tracking Work Order</a>
                    <a class="collapse-item" href="<?= $basePath ?>/customer/history">Riwayat Pesanan</a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Consumable</div>
        <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>/customer/consumable/katalog">
                <i class="fas fa-fw fa-book-open"></i>
                <span>Katalog Product</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomerConsumable">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Consumable Menu</span>
            </a>
            <div id="collapseCustomerConsumable" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Menu</h6>
                    <a class="collapse-item" href="#">Cart</a>
                    <a class="collapse-item" href="#">Checkout</a>
                    <a class="collapse-item" href="#">Tracking Order</a>
                    <a class="collapse-item" href="#">Riwayat Pesanan</a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <!-- SIDEBAR UNTUK SUPERVISOR -->
    <?php if ($currentRole === 'spv') : ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Order Management</div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSpvWO">
                <i class="fas fa-fw fa-wrench"></i>
                <span>WORK ORDER</span>
            </a>
            <div id="collapseSpvWO" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Menu</h6>
                    <a class="collapse-item" href="<?= $basePath ?>/spv/work_order/approval">Approval Management</a>
                    <a class="collapse-item" href="<?= $basePath ?>/spv/tracking">Tracking Pesanan</a>
                    <a class="collapse-item" href="<?= $basePath ?>/spv/history">History</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSpvConsumable">
                <i class="fas fa-fw fa-box-open"></i>
                <span>CONSUMABLE</span> 
            </a>
        </li>
    <?php endif; ?>

    <!-- SIDEBAR UNTUK ADMIN -->
    <?php if ($currentRole === 'admin') : ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">User Management</div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserMgmt">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Manage Users</span>
            </a>
            <div id="collapseUserMgmt" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= $basePath ?>/admin/manage/spv">Supervisor</a>
                    <a class="collapse-item" href="<?= $basePath ?>/admin/manage/customer">Customer</a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Order Management</div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdminWO">
                <i class="fas fa-fw fa-wrench"></i>
                <span>WORK ORDER</span>
            </a>
            <div id="collapseAdminWO" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Menu</h6>
                    <a class="collapse-item" href="<?= $basePath ?>/admin/tracking">Pesanan WO</a>
                    <a class="collapse-item" href="<?= $basePath ?>/admin/history">History</a>
                    <a class="collapse-item" href="#">Laporan WO</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdminConsumable">
                <i class="fas fa-fw fa-box-open"></i>
                <span>CONSUMABLE</span>
            </a>
            <div id="collapseAdminConsumable" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Menu</h6>
                    <a class="collapse-item" href="<?= $basePath ?>/admin/consumable/sections">Kelola Katalog</a>
                    <a class="collapse-item" href="#">Tracking Pesanan</a>
                    <a class="collapse-item" href="#">History</a>
                    <a class="collapse-item" href="#">Laporan Consumable</a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item">
        <a class="nav-link" href="<?= $basePath ?>/logout">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
    <div class="text-center d-none d-md-inline mt-2">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>