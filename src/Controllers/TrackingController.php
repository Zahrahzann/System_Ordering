<?php

namespace App\Controllers;

use App\Models\TrackingModel;
use App\Models\HistoryModel;
use App\Models\NotificationModel;
use App\Models\ReviewModel;
use App\Middleware\SessionMiddleware;

class TrackingController
{
    public static function index()
    {
        self::showTrackingPage();
    }

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

        $item = TrackingModel::findItemById($itemId);
        if (!$item) {
            return self::respondUpdate(false, 'Item tidak ditemukan');
        }
        $oldStatus = $item['production_status'] ?? null;

        if (!in_array($newStatus, $validStatuses)) {
            return self::respondUpdate(false, 'Status tidak valid');
        }

        // Update status
        if ($oldStatus === 'pending' && $newStatus === 'on_progress') {
            $pdo = \ManufactureEngineering\SystemOrdering\Config\Database::connect();
            $stmt = $pdo->prepare("UPDATE items 
                SET pic_mfg = ?, production_status = ?, estimasi_pengerjaan = ?, updated_at = NOW() 
                WHERE id = ?");
            $stmt->execute([$picMfg, $newStatus, $estimasi, $itemId]);
        } else {
            TrackingModel::updateItemDetails($itemId, $newStatus, $picMfg, $estimasi);
        }

        // Refresh item setelah update
        $item = TrackingModel::findItemById($itemId);

        // ========================================
        // NOTIFIKASI SYSTEM - ON PROGRESS
        // ========================================
        if ($oldStatus === 'pending' && $newStatus === 'on_progress') {
            $customerId   = $item['customer_id'];
            $departmentId = $item['department_id'];
            $orderId      = $item['order_id'];
            $itemName     = $item['product_name'] ?? $item['name'] ?? 'Item';
            $adminId      = 7; // atau ambil dari config/session

            // 1. Notifikasi untuk ADMIN
            NotificationModel::create(
                $adminId,
                "Item '{$itemName}' dari Order #{$orderId} telah dimulai produksi (On Progress)",
                'fas fa-cogs',
                'primary',
                'order',
                'admin'
            );

            // 2. Notifikasi untuk SPV
            if ($departmentId) {
                NotificationModel::create(
                    $departmentId,
                    "Item '{$itemName}' dari Order #{$orderId} sedang dalam proses produksi",
                    'fas fa-cogs',
                    'primary',
                    'order',
                    'spv'
                );
            }

            // 3. Notifikasi untuk CUSTOMER
            NotificationModel::create(
                $customerId,
                "Pesanan Anda '{$itemName}' (Order #{$orderId}) sedang dalam proses produksi! ⚙️",
                'fas fa-cogs',
                'primary',
                'order',
                'customer'
            );

            error_log("Notifikasi On Progress dibuat untuk Order #{$orderId}");
        }

        // Hitung durasi kalau status berubah ke finish
        if ($oldStatus === 'on_progress' && $newStatus === 'finish') {
            $startTime  = !empty($item['updated_at']) ? strtotime($item['updated_at']) : null;
            $finishTime = time();
            $durationMinutes = ($startTime && $finishTime > $startTime)
                ? round(($finishTime - $startTime) / 60)
                : 0;
            TrackingModel::updateItemDuration($itemId, $durationMinutes);
        }

        // Fallback: langsung pending → finish
        if ($oldStatus === 'pending' && $newStatus === 'finish') {
            TrackingModel::updateItemDuration($itemId, 0);
        }

        // ========================================
        // NOTIFIKASI SYSTEM - FINISH
        // ========================================
        if ($newStatus === 'finish') {
            $customerId   = $item['customer_id'];
            $departmentId = $item['department_id'];
            $orderId      = $item['order_id'];
            $itemName     = $item['item_name'] ?? $item['name'] ?? 'Item';
            $adminId      = 7; // atau ambil dari config/session

            // 1. Notifikasi untuk ADMIN
            NotificationModel::create(
                $adminId,
                "Item '{$itemName}' dari Order #{$orderId} telah selesai diproduksi",
                'fas fa-check-circle',
                'success',
                'order',
                'admin'
            );

            // 2. Notifikasi untuk SPV
            if ($departmentId) {
                NotificationModel::create(
                    $departmentId,
                    "Item '{$itemName}' dari Order #{$orderId} telah selesai diproduksi",
                    'fas fa-check-circle',
                    'success',
                    'order',
                    'spv'
                );
            }

            // 3. Notifikasi untuk CUSTOMER dengan order_id untuk review
            $message = "Pesanan Anda '{$itemName}' sudah selesai diproduksi! 🎉\n\nSilakan ambil barang Anda dan berikan rating & review untuk membantu kami meningkatkan layanan.";

            NotificationModel::createWithOrderId(
                $customerId,
                $message,
                'fas fa-star',
                'success',
                'review',
                'customer',
                $orderId
            );

            // Mark pending review (opsional, tergantung kebutuhan)
            // ReviewModel::markPendingReview($orderId, $customerId);

            error_log("Notifikasi Finish dibuat untuk Order #{$orderId} (dengan review prompt untuk customer)");
        }

        return self::respondUpdate(true, 'Update berhasil');
    }

    /**
     * Helper respon: AJAX → JSON, Form → redirect
     */
    private static function respondUpdate(bool $ok, string $message)
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => $ok ? 'success' : 'error',
                'message' => $message
            ]);
            exit;
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

        $_SESSION['reorder_item'] = $item;

        header('Location: /system_ordering/public/customer/work_order/form?reorder=1');
        exit;
    }

    /**
     * Customer submit review & rating
     */
    public static function submitReview()
    {
        SessionMiddleware::requireCustomerLogin();

        $orderId    = $_POST['order_id'] ?? null;
        $customerId = $_SESSION['user_data']['id'];
        $rating     = $_POST['rating'] ?? null;
        $review     = $_POST['review'] ?? null;

        header('Content-Type: application/json');

        if (!$orderId || !$rating || !$review) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Data review tidak lengkap.'
            ]);
            return;
        }

        try {
            $existing = ReviewModel::checkExisting($orderId, $customerId);
            if ($existing) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Anda sudah memberikan review untuk pesanan ini.'
                ]);
                return;
            }

            ReviewModel::submitReview($orderId, $customerId, $rating, $review);

            echo json_encode([
                'status'  => 'success',
                'message' => 'Terima kasih! Review Anda sudah tersimpan 🙏',
                'data'    => [
                    'order_id'    => $orderId,
                    'customer_id' => $customerId,
                    'rating'      => $rating,
                    'review'      => $review
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Submit review error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan review.'
            ]);
        }
    }
}
