<?php
// routes/customer.php
global $route;

// Controller
use App\Controllers\WorkOrderController;
use App\Controllers\CartController;
use App\Controllers\CustomerAuthController;
use App\Controllers\TrackingController;
use App\Controllers\DashboardController;
use App\Controllers\ConsumableController;
use App\Controllers\ConsumCartController;
use App\Controllers\ConsumOrderController;
use App\Controllers\MaterialController;
use App\Controllers\ReviewController;
use App\Models\NotificationModel;

// ROUTING DINAMIS (pakai preg_match di luar switch)
$matches = [];
if (preg_match('#^/customer/cart/edit/(\d+)$#', $route, $matches)) {
    WorkOrderController::editItem($matches[1]);
    exit;
} elseif (preg_match('#^/customer/cart/update/(\d+)$#', $route, $matches)) {
    WorkOrderController::updateItem($matches[1]);
    exit;
} elseif (preg_match('#^/customer/cart/delete/(\d+)$#', $route, $matches)) {
    CartController::deleteItem($matches[1]);
    exit;
} elseif (preg_match('#^/customer/order/delete/(\d+)$#', $route, $matches)) {
    CartController::deleteRejectedOrder($matches[1]);
    exit;
}

// ROUTING STATIS (switch-case)
switch ($route) {
    // --- AUTH & DASHBOARD ---
    case '/customer/login':
        require __DIR__ . '/../views/customer/login.php';
        break;
    case '/customer/process_login':
        CustomerAuthController::loginCustomer();
        break;
    case '/customer/dashboard':
        DashboardController::showDashboard();
        break;

    // --- WORK ORDER ---
    case '/customer/work_order/form':
        WorkOrderController::showForm();
        break;
    case '/customer/work_order/edit':
        WorkOrderController::editItem([]);
        break;
    case '/customer/work_order/process':
        WorkOrderController::processWorkOrderForm();
        break;

    case '/customer/cart': // Cart untuk work order
        CartController::showCart();
        break;
    case '/customer/checkout/confirm':
        CartController::showConfirmPage();
        break;
    case '/customer/checkout/process':
        CartController::processCheckout();
        break;
    case '/customer/checkout':
        CartController::showTrackingPage();
        break;

    // --- CONSUMABLE CART ---
    case '/customer/consumable/cart':
        ConsumCartController::showCart();
        break;
    case '/customer/consumable/cart/add':
        ConsumCartController::processAdd();
        break;
    case '/customer/consumable/cart/update':
        ConsumCartController::processUpdate();
        break;
    case '/customer/consumable/cart/delete':
        ConsumCartController::processDelete();
        break;
    case '/customer/consumable/cart/checkout':
        ConsumOrderController::processCheckout();
        break;

    // --- CONSUMABLE ORDER NOW --- 
    case '/customer/consumable/order/now':
        ConsumOrderController::orderNow();
        break;

    // --- MATERIALS (read-only untuk customer) ---
    case '/customer/materials':
        MaterialController::index();
        break;

    // --- NOTIFICATIONS --- 
    case '/customer/notification/mark':
        $id = $_POST['id'] ?? null;
        if ($id) {
            NotificationModel::markAsRead($id);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID notifikasi kosong']);
        }
        exit;

    // --- REVIEWS ---
    case '/customer/review/submit':
        ReviewController::submit();
        exit;

        // --- PESAN ERROR ---
    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route customer yang dicari: " . htmlspecialchars($route);
        break;
}
