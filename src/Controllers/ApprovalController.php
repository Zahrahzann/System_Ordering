<?php

namespace App\Controllers;

use App\Models\ApprovalModel;
use App\Middleware\SessionMiddleware;

class ApprovalController
{
    /**
     * Menampilkan halaman daftar approval untuk SPV.
     */
    public static function showApprovalListPage()
    {
        SessionMiddleware::requireSpvLogin();

        $spvId = $_SESSION['user_data']['id'];
        $pendingOrders = ApprovalModel::getPendingOrdersForSpv($spvId);

        require_once __DIR__ . '/../../views/spv/work_order/approval_wo.php';
    }

    public static function showDashboard()
    {
        SessionMiddleware::requireSpvLogin();
        $spvId = $_SESSION['user_data']['id'];

        $pendingCount = ApprovalModel::getPendingOrderCountForSpv($spvId);

        require_once __DIR__ . '/../../views/spv/dashboard.php';
    }

    public static function showDetailPage($orderId)
    {
        SessionMiddleware::requireSpvLogin();

        $order = ApprovalModel::findOrderById($orderId);
        $items = ApprovalModel::findOrderItemsByOrderId($orderId);

        if (!$order) {
            die('Order not found.');
        }

        require_once __DIR__ . '/../../views/spv/work_order/detail_approval.php';
    }

    public static function processApproval($orderId)
    { 
          SessionMiddleware::requireSpvLogin();

        $action = $_POST['action'] ?? '';
        $notes = $_POST['spv_notes'] ?? '';
        $spvId = $_SESSION['user_data']['id'];

        if ($action === 'approve') {
            ApprovalModel::updateApprovalStatus($orderId, $spvId, 'approve', $notes);
        } elseif ($action === 'reject') {
            ApprovalModel::updateApprovalStatus($orderId, $spvId, 'reject', $notes);
        }

        header('Location: /system_ordering/public/spv/work_order/approval');
        exit;
    }

    public static function getOrderDetailJson($orderId)
    {
        /** @var int|string $orderId */
        header("Content-Type: application/json");

        $order = ApprovalModel::findOrderById($orderId);
        $items = ApprovalModel::findOrderItemsByOrderId($orderId);
        $approval = ApprovalModel::findApprovalByOrderId($orderId);

        if (!$order) {
            // Kirim pesan error sebagai JSON
            echo json_encode(['error' => 'Order tidak ditemukan.']);
            exit;
        }

        // Gabungkan semua data menjadi satu array
        $responseData = [
            'order'    => $order,
            'items'    => $items,
            'approval' => $approval
        ];

        // Kirim response
        echo json_encode($responseData);
        exit;
    }
}
