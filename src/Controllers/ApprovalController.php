<?php

namespace App\Controllers;

use App\Models\ApprovalModel;
use App\Middleware\SessionMiddleware;
use App\Models\NotificationModel;
use App\Models\UserModel;

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

    /**
     * Dashboard SPV: tampilkan summary + recent orders.
     */
    public static function showDashboard()
    {
        SessionMiddleware::requireSpvLogin();
        $spvId = $_SESSION['user_data']['id'];

        // Ambil count berdasarkan status
        $pendingCount  = ApprovalModel::countByStatusForSpv($spvId, 'waiting');
        $approvedCount = ApprovalModel::countByStatusForSpv($spvId, 'approve');
        $rejectedCount = ApprovalModel::countByStatusForSpv($spvId, 'reject');

        // Tambahkan notifikasi info kalau ada pesanan baru
        if ($pendingCount > 0) {
            $_SESSION['flash_notification'] = [
                'type'    => 'info',
                'title'   => 'Pesanan Baru',
                'message' => "Ada {$pendingCount} pesanan baru menunggu approval."
            ];
        }

        // Ambil data user untuk bagian welcome
        $user = [
            'name'       => $_SESSION['user_data']['name'] ?? 'Supervisor',
            'department' => $_SESSION['user_data']['department'] ?? 'Unknown',
            'plant'      => $_SESSION['user_data']['plant'] ?? 'Unknown'
        ];

        // 🔥 Tambahan: ambil recent orders untuk tabel "Work Order Terbaru"
        $recentOrders = ApprovalModel::getRecentOrdersForSpv($spvId);

        // Kirim ke view
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

        $order = ApprovalModel::findOrderById($orderId);
        $customerId = $order['customer_id'] ?? null;

        if (in_array($action, ['approve', 'reject'])) {
            $ok = ApprovalModel::updateApprovalStatus($orderId, $spvId, $action, $notes);

            // fallback kalau belum ada baris approval
            if (!$ok) {
                ApprovalModel::createApprovalEntry($orderId, $spvId);
                $ok = ApprovalModel::updateApprovalStatus($orderId, $spvId, $action, $notes);
            }

            if ($ok) {
                $_SESSION['flash_notification'] = [
                    'type'    => $action === 'approve' ? 'success' : 'error',
                    'title'   => $action === 'approve' ? 'Berhasil!' : 'Ditolak!',
                    'message' => $action === 'approve'
                        ? 'Pesanan berhasil disetujui oleh Supervisor.'
                        : 'Pesanan ditolak oleh Supervisor.'
                ];

                // === Notifikasi untuk Customer ===
                if ($customerId) {
                    $message = $action === 'approve'
                        ? "Yeayy!! Pesanan anda sudah disetujui oleh SPV🎉 <br> Silakan lihat halaman Tracking WO!"
                        : "Upss!! Pesanan anda ditolak oleh SPV😞 <br> Mohon buat kembali Pengajuan WO atau hubungi SPV/Admin ME";

                    $icon  = $action === 'approve' ? 'fas fa-check' : 'fas fa-times';
                    $color = $action === 'approve' ? 'success' : 'danger';

                    $existing = NotificationModel::findUnreadByMessage(
                        $customerId,
                        'customer',
                        'order_status',
                        $message
                    );

                    if (!$existing) {
                        NotificationModel::create(
                            $customerId,
                            $message,
                            $icon,
                            $color,
                            'order_status',
                            'customer'
                        );
                    }
                }

                // === Notifikasi untuk Admin (hanya kalo approve) ===
                if ($action === 'approve') {
                    $adminMessage = "Pesanan Work Order #{$orderId} sudah disetujui SPV dan siap diproses oleh Admin.";
                    $adminIcon    = 'fas fa-clipboard-check';
                    $adminColor   = 'info';

                    $admins = UserModel::getAllAdmins();
                    foreach ($admins as $admin) {
                        $existingAdminNotif = NotificationModel::findUnreadByMessage(
                            $admin['id'],
                            'admin',
                            'order_status',
                            $adminMessage
                        );

                        if (!$existingAdminNotif) {
                            NotificationModel::create(
                                $admin['id'],
                                $adminMessage,
                                $adminIcon,
                                $adminColor,
                                'order_status',
                                'admin'
                            );
                        }
                    }
                }
            } else {
                $_SESSION['flash_notification'] = [
                    'type'    => 'error',
                    'title'   => 'Gagal!',
                    'message' => 'Pesanan gagal diproses. Silakan hubungi admin.'
                ];
            }

            header('Location: /system_ordering/public/spv/work_order/approval');
            exit;
        }

        // fallback kalau action tidak valid
        $_SESSION['flash_notification'] = [
            'type'    => 'error',
            'title'   => 'Invalid',
            'message' => 'Aksi tidak dikenali.'
        ];
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
