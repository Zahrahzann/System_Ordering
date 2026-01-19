<?php

namespace App\Controllers;

use App\Models\TrackingModel;
use App\Models\HistoryModel;
use App\Models\NotificationModel;
use App\Models\ReviewModel;
use App\Middleware\SessionMiddleware;

class TrackingController
{
    /**
     * Menampilkan halaman tracking (multi-role)
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

        // Grouping per order_id
        $grouped = [];
        foreach ($items as $item) {
            $orderId = $item['order_id'];
            if (!isset($grouped[$orderId])) {
                $grouped[$orderId] = [
                    'order_details' => [
                        'order_id'        => $orderId,
                        'customer_name'   => $item['customer_name'],
                        'line'            => $item['line'],
                        'department_name' => $item['department_name'],
                    ],
                    'items' => []
                ];
            }

            // inject estimasi dari session kalau ada
            if (isset($_SESSION['estimasi'][$item['item_id']])) {
                $item['estimasi'] = $_SESSION['estimasi'][$item['item_id']];
            }

            $grouped[$orderId]['items'][] = $item;
        }

        // Tentukan emergency type di level order
        foreach ($grouped as &$order) {
            $types = array_column($order['items'], 'emergency_type');
            if (in_array('line_stop', $types)) {
                $order['order_details']['emergency_type'] = 'line_stop';
            } elseif (in_array('safety', $types)) {
                $order['order_details']['emergency_type'] = 'safety';
            } else {
                $order['order_details']['emergency_type'] = 'regular';
            }
        }

        // Kirim $grouped ke view
        $orders = array_values($grouped);

        require_once __DIR__ . '/../../views/shared/tracking_order.php';
    }

    /**
     * (Hanya Admin) Memproses update status produksi
     */
    public static function updateItemDetails($itemId)
    {
        SessionMiddleware::requireAdminLogin();
        $newStatus = $_POST['status'] ?? '';
        $picMfg    = trim($_POST['pic_mfg'] ?? '');
        $estimasi  = trim($_POST['estimasi_pengerjaan'] ?? '');

        $validStatuses = ['pending', 'on_progress', 'finish', 'completed'];

        // Ambil item sebelum update
        $item = TrackingModel::findItemById($itemId);
        $oldStatus = $item['production_status'] ?? null;

        // Validasi status
        if (!in_array($newStatus, $validStatuses)) {
            header('Location: /system_ordering/public/admin/tracking');
            exit;
        }

        // Update status
        if ($oldStatus === 'pending' && $newStatus === 'on_progress') {
            $pdo = \ManufactureEngineering\SystemOrdering\Config\Database::connect();
            $stmt = $pdo->prepare("UPDATE items 
                SET pic_mfg = ?, 
                    production_status = ?, 
                    estimasi_pengerjaan = ?, 
                    updated_at = NOW() 
                WHERE id = ?");
            $stmt->execute([$picMfg, $newStatus, $estimasi, $itemId]);
        } else {
            TrackingModel::updateItemDetails($itemId, $newStatus, $picMfg, $estimasi);
        }

        // Refresh item setelah update
        $item = TrackingModel::findItemById($itemId);

        // Hitung durasi kalau status berubah ke finish
        if ($oldStatus === 'on_progress' && $newStatus === 'finish') {
            $startTime  = !empty($item['updated_at']) ? strtotime($item['updated_at']) : null;
            $finishTime = time();

            if ($startTime && $finishTime > $startTime) {
                $durationMinutes = round(($finishTime - $startTime) / 60);
                TrackingModel::updateItemDuration($itemId, $durationMinutes);
            } else {
                TrackingModel::updateItemDuration($itemId, 0);
            }
        }

        // Fallback: langsung pending → finish
        if ($oldStatus === 'pending' && $newStatus === 'finish') {
            TrackingModel::updateItemDuration($itemId, 0);
        }

        // Jika status berubah ke finish → kirim notifikasi + flag review
        if ($newStatus === 'finish') {
            NotificationModel::create(
                $item['customer_id'], 
                'Pesanan #' . $item['order_id'] . ' HALOO!! Work Order kamu sudah selesai, segera ambil barang di ME ya, Terima kasih sudah menggunakan SOME. Mohon untuk isi Rating dan Reviewnya yaaa~~ , ', // message
                'fas fa-check-circle',
                'success',            
                'order',              
                'customer'             
            );

            ReviewModel::markPendingReview($item['order_id'], $item['customer_id']);
        }

        header('Location: /system_ordering/public/admin/tracking');
        exit;
    }

    /**
     * Menampilkan Halaman Detail WO (Multi-role)
     */
    public static function showDetailPage($orderId)
    {
        SessionMiddleware::requireLogin();
        $order    = \App\Models\ApprovalModel::findOrderById($orderId);
        $items    = \App\Models\ApprovalModel::findOrderItemsByOrderId($orderId);
        $approval = \App\Models\ApprovalModel::findApprovalByOrderId($orderId);

        if (!$order) {
            die('Error: Pesanan tidak ditemukan.');
        }

        require_once __DIR__ . '/../../views/shared/detail_wo.php';
    }

    /**
     * Menampilkan halaman history order (multi-role)
     */
    public static function showHistoryPage()
    {
        SessionMiddleware::requireLogin();

        $role  = $_SESSION['user_data']['role'];
        $year  = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;

        $items     = [];
        $chartData = [];

        if ($role === 'admin') {
            $items     = HistoryModel::getHistoryItems(null, $year, $month);
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

    /**
     * Customer reorder item dari history
     */
    public static function reorderItem($itemId)
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'];

        $item = HistoryModel::findItemById($itemId);
        if (!$item || $item['customer_id'] != $customerId) {
            die('Item tidak valid atau bukan milik Anda.');
        }

        // Simpan data ke session untuk prefill form
        $_SESSION['reorder_item'] = $item;

        // Redirect ke form WO
        header('Location: /system_ordering/public/customer/work_order/form?reorder=1');
        exit;
    }
}
