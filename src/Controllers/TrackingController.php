<?php

namespace App\Controllers;

use App\Models\TrackingModel;
use App\Models\HistoryModel;
use App\Middleware\SessionMiddleware;

class TrackingController
{
    /**
     * Menampilkan halaman tracking (AKTIF)
     */
    public static function showTrackingPage()
    {
        SessionMiddleware::requireLogin();
        $role = $_SESSION['user_data']['role'];
        $items = [];

        if ($role === 'admin') {
            $items = TrackingModel::getApprovedItemsByPriority();
        } elseif ($role === 'spv') {
            $departmentId = $_SESSION['user_data']['department_id'];
            $items = TrackingModel::getApprovedItemsByPriority($departmentId);
        } elseif ($role === 'customer') {
            $customerId = $_SESSION['user_data']['id'];
            $items = TrackingModel::getItemsByCustomer($customerId);
        }

        require_once __DIR__ . '/../../views/shared/tracking_order.php';
    }

    /**
     * (Hanya Admin) Memproses update status
     */
    public static function updateItemDetails($itemId)
    {
        /** @var int|string $itemId */
        SessionMiddleware::requireAdminLogin();
        $newStatus = $_POST['status'] ?? '';
        $picMfg = trim($_POST['pic_mfg'] ?? '');
        $validStatuses = ['pending', 'on_progress', 'finish', 'completed'];

        if (in_array($newStatus, $validStatuses)) {
            TrackingModel::updateItemDetails($itemId, $newStatus, $picMfg);
        }
        header('Location: /system_ordering/public/admin/tracking');
        exit;
    }

    /**
     * Menampilkan Halaman Detail WO (Multi-role)
     */
    public static function showDetailPage($orderId)
    {
        /** @var int|string $orderId */
        SessionMiddleware::requireLogin();
        $order = \App\Models\ApprovalModel::findOrderById($orderId);
        $items = \App\Models\ApprovalModel::findOrderItemsByOrderId($orderId);
        $approval = \App\Models\ApprovalModel::findApprovalByOrderId($orderId);
        if (!$order) {
            die('Error: Pesanan tidak ditemukan.');
        }
        require_once __DIR__ . '/../../views/shared/detail_wo.php';
    }


    /**
     * KOREKSI: FUNGSI showHistoryPage() SEKARANG MENGGUNAKAN HISTORY MODEL
     */
    public static function showHistoryPage()
    {
        SessionMiddleware::requireLogin();

        $role = $_SESSION['user_data']['role'];

        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;

        $items = [];
        $chartData = [];

        if ($role === 'admin') {
            $items = HistoryModel::getHistoryItems(null, $year, $month);
            $chartData = HistoryModel::getMonthlyChartData($year);
        } elseif ($role === 'spv') {
            $departmentId = $_SESSION['user_data']['department_id'];
            $items = HistoryModel::getHistoryItems($departmentId, $year, $month);
        } elseif ($role === 'customer') {
            $customerId = $_SESSION['user_data']['id'];
            $items = HistoryModel::getHistoryItemsByCustomer($customerId, $year, $month);
        }

        $availableYears = range(date('Y'), date('Y') - 5);

        require_once __DIR__ . '/../../views/shared/history_order.php';
    }

    public static function reorderItem($itemId)
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'];

        // Ambil item dari histori
        $item = HistoryModel::findItemById($itemId);
        if (!$item || $item['customer_id'] != $customerId) {
            die('Item tidak valid atau bukan milik Anda.');
        }

        // Tambahkan ke keranjang baru (simulasi)
        \App\Models\CartModel::addItemToCart($customerId, $item);

        $_SESSION['flash_message'] = 'Item berhasil ditambahkan kembali ke keranjang.';
        header('Location: /system_ordering/public/customer/cart');
        exit;
    }
}
