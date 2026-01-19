<?php // routes/web.php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $route;

// Normalisasi route agar cocok dengan case di bawah 
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = preg_replace('#^/system_ordering/public#', '', $route);

$requestMethod = $_SERVER['REQUEST_METHOD'];

error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("PARSED ROUTE: " . $route);
error_log("METHOD: " . $requestMethod);
if (!empty($_POST)) {
    error_log("POST DATA: " . print_r($_POST, true));
} else {
    error_log("POST DATA: kosong");
}

// 🔍 Debug log setelah variabel ada
error_log("Route: $route, Method: $requestMethod");

// Tambahan debug ke browser 
//echo "<pre>DEBUG ROUTE: $route, METHOD: $requestMethod</pre>";

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
use App\Controllers\MaterialController;
use App\Controllers\WorkOrderCostController;
use App\Controllers\ConsumableReportController;

// =====================
// ROUTING DINAMIS (Regex)
// =====================
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

if (preg_match('#^/customer/workorder/reorder/(\d+)$#', $route, $matches)) {
    HistoryController::reorderItem($matches[1]);
    exit;
}

// =====================
// WORK ORDER COST ROUTES
// =====================

if (preg_match('#^/admin/workorder/savecost/?$#', $route) && $requestMethod === 'POST') {
    WorkOrderCostController::saveCost();
    exit;
}

if (preg_match('#^/(admin|spv|customer)/report/workorder$#', $route)) {
    WorkOrderCostController::showMonthlyReport();
    exit;
}

// ===== CONSUMABLE REPORT ===== 
if ($route === '/admin/consumable/report' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    ConsumableReportController::showReport();
    exit;
}
if ($route === '/admin/consumable/report/save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ConsumableReportController::saveReport();
    exit;
}

// =====================
// ROUTES UTAMA
// =====================
switch (true) {
    // Root route → Landing Page 
    case ($route === '/'):
        require __DIR__ . '/../views/landing_page.php';
        break;

    // Logout
    case ($route === '/logout'):
        if (session_status() === PHP_SESSION_NONE) session_start();
        $role = $_SESSION['user_data']['role'] ?? 'customer';
        if ($role === 'admin' || $role === 'spv') {
            AuthController::logout();
        } else {
            CustomerAuthController::logout();
        }
        break;

    // Tracking
    case ($route === '/customer/tracking' || $route === '/spv/tracking' || $route === '/admin/tracking'):
        TrackingController::showTrackingPage();
        break;

    // History
    case ($route === '/admin/history' || $route === '/spv/history' || $route === '/customer/history'):
        HistoryController::showHistoryPage();
        break;

    // Consumable sections
    case ($route === '/customer/consumable/sections' || $route === '/spv/consumable/sections' || $route === '/admin/consumable/sections'):
        ConsumableController::listSection();
        break;

    // =====================
    // MATERIAL ROUTES (AJAX)
    // =====================
    case ($route === '/materials/types'):
        MaterialController::getTypes();
        break;

    case ($route === '/materials/dimensions' && isset($_GET['type_id'])):
        MaterialController::getDimensionsByType($_GET['type_id']);
        break;

    case preg_match('#^/materials/dimension/(\d+)$#', $route, $matches):
        MaterialController::showDimension($matches[1]);
        break;

    // =====================
    // PRODUCT TYPE ROUTES
    // =====================
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

    case ($route === '/admin/consumable/product-types'):
        $sectionId = $_GET['section'] ?? null;
        if ($sectionId && is_numeric($sectionId)) {
            ProductTypeController::listBySection($sectionId);
        } else {
            echo "Error: section ID tidak valid.";
        }
        break;

    // =====================
    // PRODUCT ITEM ROUTES
    // =====================
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

    case ($route === '/admin/consumable/product-items'):
        if (isset($_GET['type'])) {
            ProductItemController::listByProductType($_GET['type']);
        } else {
            echo "Error: type tidak ditemukan.";
        }
        break;

    // =====================
    // CONSUMABLE ORDER ROUTES
    // =====================
    case ($route === '/customer/shared/consumable/orders' || $route === '/spv/shared/consumable/orders' || $route === '/admin/shared/consumable/orders'):
        ConsumOrderController::showOrders();
        break;

    case ($route === '/customer/shared/consumable/reorder'):
        ConsumOrderController::reorder();
        break;

    case preg_match('#^/admin/consumable/orders/send/(\d+)$#', $route, $matches):
        ConsumOrderController::sendOrder($matches[1]);
        break;

    case preg_match('#^/admin/consumable/orders/complete/(\d+)$#', $route, $matches):
        ConsumOrderController::completeOrder($matches[1]);
        break;

    case preg_match('#^/admin/consumable/orders/delete/(\d+)$#', $route, $matches):
        ConsumOrderController::deleteOrder($matches[1]);
        break;

    // =====================
    // CONSUMABLE HISTORY ROUTES
    // =====================
    case ($route === '/customer/consumable/history' || $route === '/spv/consumable/history' || $route === '/admin/consumable/history'):
        ConsumHistoryController::showHistory();
        break;

    // =====================
    // DEFAULT 404
    // =====================
    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route yang dicari: " . htmlspecialchars($route);
        break;
}
