<?php
// routes/spv.php
// Variabel $route dari index.php
global $route;

// CONTROLLER
use App\Controllers\AuthController;
use App\Controllers\ApprovalController;
use App\Controllers\TrackingController;

// ROUTING DINAMIS
$matches = [];
if (preg_match('#^/spv/work_order/detail/(\d+)$#', $route, $matches)) {
    ApprovalController::showDetailPage($matches[1]);
    exit;
} elseif (preg_match('#^/spv/work_order/process_approval/(\d+)$#', $route, $matches)) {
    ApprovalController::processApproval($matches[1]);
    exit;
}

// ROUTING STATIS
switch ($route) {
    case '/spv/register':
        AuthController::showRegisterPage('spv');
        break;
    case '/spv/process_register':
        AuthController::registerUser('spv');
        break;
    case '/spv/login':
        require __DIR__ . '/../views/spv/login_spv.php'; 
        break;
    case '/spv/process_login':
        AuthController::loginUser('spv');
        break;
    case '/spv/dashboard':
        ApprovalController::showDashboard();
        break;
    case '/spv/work_order/approval':
        ApprovalController::showApprovalListPage();
        break;
        
    default:
        http_response_code(404);
        echo "404 Not Found :) <br>";
        echo "Route SPV yang dicari: " . htmlspecialchars($route);
        break;
}
