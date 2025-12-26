<?php
ob_clean();
session_start(); // penting supaya $_SESSION bisa diakses

require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/ProductItemModel.php';
require_once __DIR__ . '/../src/Models/NotificationModel.php';

use App\Models\ProductItemModel;
use App\Models\NotificationModel;

header('Content-Type: application/json');

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

        // Buat notif admin
        try {
            NotificationModel::create(
                $userId,
                "Stok produk {$item['name']} tinggal {$item['stock']}, segera kelola!",
                'fas fa-box',
                'danger',
                'stock_alert',
                'admin'
            );
        } catch (Exception $e) {
            // kalau gagal insert notif, tetap lanjut return JSON
            error_log("Notif error: " . $e->getMessage());
        }
    }

    echo json_encode([
        'alert' => true,
        'message' => "Produk berikut memiliki stok ≤ 10:<br>" . implode('<br>', $messages)
    ]);
} else {
    echo json_encode(['alert' => false]);
}
