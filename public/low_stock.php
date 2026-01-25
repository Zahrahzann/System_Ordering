<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/ProductItemModel.php';
require_once __DIR__ . '/../src/Models/NotificationModel.php';

use App\Models\ProductItemModel;
use App\Models\NotificationModel;

// Set header JSON
header('Content-Type: application/json');

// Clear any previous output
if (ob_get_level()) {
    ob_clean();
}

try {
    // Ambil id user yang sedang login dari session
    $userId = $_SESSION['user_data']['id'] ?? null;

    // Kalau session kosong, fallback ke id admin (misalnya 7)
    if ($userId === null) {
        $userId = 7;
    }

    $lowStockItems = ProductItemModel::getLowStockItems(10);

    if (!empty($lowStockItems)) {
        $messages = [];

        foreach ($lowStockItems as $item) {
            $messages[] = "{$item['name']} (stok: {$item['stock']})";
        }

        // Buat SATU notif untuk semua produk low stock
        $fullMessage = "Produk berikut memiliki stok ≤ 10:\n" . implode("\n", $messages);

        // Cek apakah sudah ada notif low stock yang belum dibaca
        $existingNotif = NotificationModel::findUnreadByMessage($userId, 'admin', 'stock_alert', $fullMessage);

        if ($existingNotif) {
            // Sudah ada, return notif yang existing
            echo json_encode([
                'alert'   => true,
                'message' => nl2br($fullMessage),
                'id'      => $existingNotif['id'],
                'count'   => count($lowStockItems),
                'existing' => true
            ]);
        } else {
            // Belum ada, buat baru
            try {
                $notifId = NotificationModel::create(
                    $userId,
                    $fullMessage,
                    'fas fa-box',
                    'warning',
                    'stock_alert',
                    'admin'
                );

                error_log("Low stock notification created with ID: " . $notifId);

                echo json_encode([
                    'alert'   => true,
                    'message' => nl2br($fullMessage),
                    'id'      => $notifId,
                    'count'   => count($lowStockItems),
                    'existing' => false
                ]);
            } catch (Exception $e) {
                error_log("Notif creation error: " . $e->getMessage());
                echo json_encode([
                    'alert' => false,
                    'error' => 'Gagal membuat notifikasi: ' . $e->getMessage()
                ]);
            }
        }
    } else {
        echo json_encode([
            'alert' => false,
            'message' => 'Semua stok produk aman'
        ]);
    }
} catch (Exception $e) {
    error_log("low_stock.php error: " . $e->getMessage());
    echo json_encode([
        'alert' => false,
        'error' => $e->getMessage()
    ]);
}
exit;
