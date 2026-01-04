<?php

use App\Models\NotificationModel;
use App\Models\ProductItemModel;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

$userData   = $_SESSION['user_data'] ?? [];
$role       = $userData['role'] ?? null;
$department = $userData['department_id'] ?? null;
$userId     = $userData['id'] ?? null;

$alerts = [];
$count  = 0;

// --- SPV Notif: pengajuan WO dari departemen customer yang sama
if ($role === 'spv') {
    // Ambil notif unread
    $alerts = NotificationModel::getLatest($department, 'spv');

    if (!empty($alerts)) {
        // Tandai notif terbaru sebagai sudah dibaca
        NotificationModel::markAsRead($alerts[0]['id']);
    }

    // Hitung ulang setelah tandai
    $count  = NotificationModel::countUnread($department, 'spv');
}

// --- Admin Notif: stok rendah
if ($role === 'admin') {
    // Ambil notif unread
    $alerts = NotificationModel::getLatest($userId, 'admin');
 
    // Cek stok rendah
    $lowStockItems = ProductItemModel::getLowStockItems(10);
    foreach ($lowStockItems as $item) {
        $message = "Stok produk {$item['name']} tinggal {$item['stock']}, segera kelola!";
        $existing = NotificationModel::findUnreadByMessage(
            $userId,
            'admin',
            'stock_alert',
            $message
        );

        if (!$existing) {
            NotificationModel::createUnique(
                $userId,
                $message,
                'fas fa-box',
                'danger',
                'stock_alert',
                'admin'
            );
        }
    }

    // Refresh alerts & count setelah insert
    $alerts = NotificationModel::getLatest($userId, 'admin');
    $count  = NotificationModel::countUnread($userId, 'admin');
}

// --- Customer Notif: status pesanan
if ($role === 'customer') {
    // Ambil notif unread
    $alerts = NotificationModel::getLatest($userId, 'customer');

    if (!empty($alerts)) {
        // Tandai notif terbaru sebagai sudah dibaca
        NotificationModel::markAsRead($alerts[0]['id']);
    }

    // Hitung ulang setelah tandai
    $count  = NotificationModel::countUnread($userId, 'customer');
}

// --- Output JSON untuk pop-up dan badge
if (!empty($alerts)) {
    $latest = $alerts[0];
    echo json_encode([
        'new'     => true,
        'id'      => $latest['id'],
        'count'   => $count,
        'message' => nl2br($latest['message']), 
        'type'    => $latest['color'] ?? 'info',
        'icon'    => $latest['icon'] ?? 'info'
    ]);
} else {
    echo json_encode(['new' => false, 'count' => 0]);
}

