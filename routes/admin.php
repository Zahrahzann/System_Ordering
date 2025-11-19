<?php
// routes/admin.php
// Variabel $route dari index.php
global $route;

// CONTROLLER
use App\Controllers\AuthController;
use App\Controllers\TrackingController;
use App\Controllers\AdminController;
use App\Controllers\UserManagementController;
use App\Controllers\Admin\ConsumableController;

// ROUTING DINAMIS
$matches = [];
if (preg_match('#^/admin/tracking/update_item/(\d+)$#', $route, $matches)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        TrackingController::updateItemDetails($matches[1]);
    }
    exit;
}

// ROUTING STATIS
switch ($route) {
    case '/admin/register':
        AuthController::showRegisterPage('admin');
        break;
    case '/admin/process_register':
        AuthController::registerUser('admin');
        break;
    case '/admin/login':
        AdminController::showLoginPage();
        break;
    case '/admin/process_login':
        AuthController::loginUser('admin');
        break;
    case '/admin/dashboard':
        AdminController::showDashboard();
        break;

    // MANAGEMENT USER
    case '/admin/manage/spv':
        UserManagementController::listSpv();
        break;
    case '/admin/manage/customer';
        UserManagementController::listCustomers();
        break;

    // MANAGEMENT CONSUMABLE

    case '/admin/consumable/katalog_kategori':
        \App\Controllers\ConsumableController::listCategories();
        break;

    case '/admin/consumable/katalog_kategori/add':
        \App\Controllers\ConsumableController::addCategory();
        break;

    case '/admin/consumable/katalog_kategori/edit':
        \App\Controllers\ConsumableController::editCategory($_GET['id']);
        break;

    case '/admin/consumable/katalog_kategori/delete':
        \App\Controllers\ConsumableController::deleteCategory($_GET['id']);
        break;

    case '/admin/consumable/katalog_produk':
        \App\Controllers\ConsumableController::listProducts();
        break;

    case '/admin/consumable/katalog_produk/add':
        \App\Controllers\ConsumableController::addProduct();
        break;

    case '/admin/consumable/katalog_produk/edit':
        \App\Controllers\ConsumableController::editProduct($_GET['id']);
        break;

    case '/admin/consumable/katalog_produk/delete':
        \App\Controllers\ConsumableController::deleteProduct($_GET['id']);
        break;


    // 404 NOT FOUND RESPON
    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route Admin yang dicari: " . htmlspecialchars($route);
        break;
}
