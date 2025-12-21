<?php
// routes/admin.php
global $route;

// CONTROLLER
use App\Controllers\AuthController;
use App\Controllers\TrackingController;
use App\Controllers\AdminController;
use App\Controllers\UserManagementController;
use App\Controllers\ConsumableController;
use App\Controllers\MaterialController;

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

    case '/admin/manage/customer':
        UserManagementController::listCustomers();
        break;

    // MANAGEMENT CONSUMABLE (admin only)
    case '/admin/consumable/sections':
        ConsumableController::listSection();
        break;

    case '/admin/consumable/sections/add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ConsumableController::addSection();
        }
        break;

    case '/admin/consumable/sections/edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ConsumableController::editSection($_POST['id']);
        }
        break;

    case '/admin/consumable/sections/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            ConsumableController::deleteSection($_GET['id']);
        }
        break;

    // 404 NOT FOUND RESPON (untuk route statis)
    default:
        // jangan langsung exit, biarkan switch (true) di bawah bisa jalan
        break;
}

// =====================
// MANAGEMENT MATERIAL (admin only)
// =====================
switch (true) {
    // Index
    case ($route === '/admin/materials'):
        MaterialController::index();
        break;

    // ===== TYPE =====
    case ($route === '/admin/materials/type/store' && $_SERVER['REQUEST_METHOD'] === 'POST'):
        MaterialController::storeType();
        break;

    case preg_match('#^/admin/materials/type/update/(\d+)$#', $route, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST':
        MaterialController::updateType($matches[1]);
        break;

    case preg_match('#^/admin/materials/type/delete/(\d+)$#', $route, $matches):
        MaterialController::destroyType($matches[1]);
        break;

    // ===== DIMENSION =====
    case ($route === '/admin/materials/dimension/store' && $_SERVER['REQUEST_METHOD'] === 'POST'):
        MaterialController::storeDimension();
        break;

    case preg_match('#^/admin/materials/dimension/update/(\d+)$#', $route, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST':
        MaterialController::updateDimension($matches[1]);
        break;

    case preg_match('#^/admin/materials/dimension/delete/(\d+)$#', $route, $matches):
        MaterialController::destroyDimension($matches[1]);
        break;

    // default:
    //     http_response_code(404);
    //     echo "404 Not Found :) <br>";
    //     echo "Route Admin yang dicari: " . htmlspecialchars($route);
    //     break;
}
