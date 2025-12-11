<?php
// routes/web.php
// Variabel $route dari index.php
global $route;

// CONTROLLER
use App\Controllers\AuthController;
use App\Controllers\CustomerAuthController;
use App\Controllers\TrackingController;
use App\Controllers\HistoryController;
use App\Controllers\ConsumableController;
use App\Controllers\ProductTypeController;
use App\Controllers\ProductItemController;
use App\Controllers\ConsumOrderController;
use App\Controllers\ConsumHistoryController;

$requestMethod = $_SERVER['REQUEST_METHOD'];

// ROUTING DINAMIS
$matches = [];
if (preg_match('#^/(admin|spv|customer)/tracking/detail/(\d+)$#', $route, $matches)) {
    TrackingController::showDetailPage($matches[2]);
    exit;
}

if (preg_match('#^/admin/tracking/update_item/(\d+)$#', $route, $matches)) {
    TrackingController::updateItemDetails($matches[1]);
    exit;
}

if (preg_match('#^/customer/history/reorder/(\d+)$#', $route, $matches)) {
    HistoryController::reorderItem($matches[1]);
    exit;
}


// =====================
// WORK ORDER ROUTES  
// =====================
switch ($route) {
    case '/':
        header('Location: /system_ordering/public/customer/login');
        exit;

    case '/logout':
        if (session_status() === PHP_SESSION_NONE) session_start();
        $role = $_SESSION['user_data']['role'] ?? 'customer';
        if ($role === 'admin' || $role === 'spv') {
            AuthController::logout();
        } else {
            CustomerAuthController::logout();
        }
        break;

    // Rute tracking statis
    case '/customer/tracking':
    case '/spv/tracking':
    case '/admin/tracking':
        TrackingController::showTrackingPage();
        break;

    case '/admin/tracking/update_item':
        $itemId = $_POST['item_id'] ?? null;
        if ($itemId) {
            TrackingController::updateItemDetails($itemId);
        } else {
            echo "Error: item_id tidak ditemukan.";
        }
        break;

    case '/admin/history':
    case '/spv/history':
    case '/customer/history':
        HistoryController::showHistoryPage();
        break;

    // ==================================
    // CONSUMABLE ROUTES (SECTION LIST) 
    // ==================================
    case '/customer/consumable/sections':
    case '/spv/consumable/sections':
    case '/admin/consumable/sections':
        ConsumableController::listSection();
        break;
}

// ROUTING DINAMIS PRODUCT TYPE & ITEM
switch (true) {
    // PRODUCT TYPE ROUTES
    case preg_match('#^/shared/consumable/product-types/(\d+)$#', $route, $matches):
        ProductTypeController::listBySection($matches[1]);
        break;

    case preg_match('#^/shared/consumable/product-type/(\d+)$#', $route, $matches):
        ProductTypeController::detail($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-types/create/(\d+)$#', $route, $matches):
        ProductTypeController::add($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-types/edit/(\d+)$#', $route, $matches):
        ProductTypeController::edit($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-types/delete/(\d+)$#', $route, $matches):
        ProductTypeController::delete($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-types$#', $route):
        $sectionId = $_GET['section'] ?? null;
        if ($sectionId && is_numeric($sectionId)) {
            ProductTypeController::listBySection($sectionId);
        } else {
            echo "Error: section ID tidak valid.";
        }
        break;

    // PRODUCT ITEM ROUTES
    case preg_match('#^/shared/consumable/product-items/(\d+)$#', $route, $matches):
        ProductItemController::listByProductType($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-items/create/(\d+)$#', $route, $matches):
        ProductItemController::add($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-items/edit/(\d+)$#', $route, $matches):
        ProductItemController::edit($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-items/delete/(\d+)$#', $route, $matches):
        ProductItemController::delete($matches[1]);
        break;

    case preg_match('#^/admin/consumable/product-items$#', $route):
        if (isset($_GET['type'])) {
            ProductItemController::listByProductType($_GET['type']);
        } else {
            echo "Error: type tidak ditemukan.";
        }
        break;

    // CONSUMABLE ORDER ROUTES
    case ($route === '/customer/shared/consumable/orders'
        || $route === '/spv/shared/consumable/orders'
        || $route === '/admin/shared/consumable/orders'):
        ConsumOrderController::showOrders();
        break;
    case ($route === '/customer/shared/consumable/reorder'):
        ConsumOrderController::reorder();
        break;

    // ACTION ROUTES (Admin only)
    case preg_match('#^/admin/consumable/orders/send/(\d+)$#', $route, $matches):
        ConsumOrderController::sendOrder($matches[1]);
        break;

    case preg_match('#^/admin/consumable/orders/complete/(\d+)$#', $route, $matches):
        ConsumOrderController::completeOrder($matches[1]);
        break;

    case preg_match('#^/admin/consumable/orders/delete/(\d+)$#', $route, $matches):
        ConsumOrderController::deleteOrder($matches[1]);
        break;


    // CONSUMABLE HISTORY ROUTES
    case '/customer/consumable/history':
    case '/spv/consumable/history':
    case '/admin/consumable/history':
        ConsumHistoryController::showHistory();
        break;
        
    // Default 404
    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route yang dicari: " . htmlspecialchars($route);
        break;
}
