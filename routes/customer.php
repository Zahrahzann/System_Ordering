<?php
// routes/customer.php
// Variabel $route dari index.php
global $route;

// Controller
use App\Controllers\WorkOrderController;
use App\Controllers\CartController;
use App\Controllers\CustomerAuthController;
use App\Controllers\TrackingController;
use App\Controllers\DashboardController;
use App\Controllers\ConsumableController;


// ROUTING DINAMIS
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
}

// ROUTING STATIS
switch ($route) {
    case '/customer/login':
        require __DIR__ . '/../views/customer/login.php';
        break;
    case '/customer/process_login':
        CustomerAuthController::loginCustomer();
        break;
    case '/customer/dashboard':
        DashboardController::showDashboard();
        break;
    case '/customer/work_order/form':
        WorkOrderController::showForm();
        break;
    case '/customer/work_order/edit':
        WorkOrderController::editItem([]);
        break;
    case '/customer/work_order/process_add_to_cart':
        WorkOrderController::processWorkOrderForm();
        break;
    case '/customer/cart':
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

    // CONSUMABLE AREA CUSTOMER
    case '/customer/consumable/katalog':
        ConsumableController::showCatalog();
        break;



    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route customer yang dicari: " . htmlspecialchars($route);
        break;
}
