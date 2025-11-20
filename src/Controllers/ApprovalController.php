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
        $orders = ApprovalModel::getOrdersForSpv($spvId);
        $user = $_SESSION['user_data'];

        require_once __DIR__ . '/../../views/spv/work_order/approval_wo.php';
    }

    public static function showDashboard()
    {
        SessionMiddleware::requireSpvLogin();
        $spvId = $_SESSION['user_data']['id'];

        // Ambil count berdasarkan status
        $pendingCount  = ApprovalModel::countByStatusForSpv($spvId, 'waiting');
        $approvedCount = ApprovalModel::countByStatusForSpv($spvId, 'approve');
        $rejectedCount = ApprovalModel::countByStatusForSpv($spvId, 'reject');

        // Ambil data user untuk bagian welcome
        $user = [
            'name'       => $_SESSION['user_data']['name'] ?? 'Supervisor',
            'department' => $_SESSION['user_data']['department'] ?? 'Unknown',
            'plant'      => $_SESSION['user_data']['plant'] ?? 'Unknown'
        ];

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
        $notes  = $_POST['spv_notes'] ?? '';
        $spvId  = $_SESSION['user_data']['id'];

        if ($action === 'approve') {
            $ok = ApprovalModel::updateApprovalStatus($orderId, $spvId, 'approve', $notes);
            if (!$ok) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_error'] = 'Gagal menyetujui order. Silakan hubungi admin.';
            }
            header('Location: /system_ordering/public/admin/tracking');
            exit;
        } elseif ($action === 'reject') {
            $ok = ApprovalModel::updateApprovalStatus($orderId, $spvId, 'reject', $notes);
            if (!$ok) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_error'] = 'Gagal menolak order. Silakan hubungi admin.';
            }
            // ✅ Balik ke halaman approval SPV
            header('Location: /system_ordering/public/spv/work_order/approval');
            exit;
        }

        // fallback kalau action tidak valid
        header('Location: /system_ordering/public/spv/work_order/approval');
        exit;
    }

    public static function getOrderDetailJson($orderId)
    {
        header("Content-Type: application/json");

        $order    = ApprovalModel::findOrderById($orderId);
        $items    = ApprovalModel::findOrderItemsByOrderId($orderId);
        $approval = ApprovalModel::findApprovalByOrderId($orderId);

        if (!$order) {
            echo json_encode(['error' => 'Order tidak ditemukan.']);
            exit;
        }

        $responseData = [
            'order'    => $order,
            'items'    => $items,
            'approval' => $approval
        ];

        echo json_encode($responseData);
        exit;
    }
}
