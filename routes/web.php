<?php
// routes/web.php
// Variabel $route dari index.php
global $route;

// CONTROLLER
use App\Controllers\AuthController;
use App\Controllers\CustomerAuthController;
use App\Controllers\TrackingController;
use App\Controllers\HistoryController;

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




// ROUTING STATIS
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

    // --- Default 404 ---
    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route yang dicari: " . htmlspecialchars($route);
        break;
}
