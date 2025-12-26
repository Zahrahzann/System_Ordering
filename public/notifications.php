<?php

use App\Models\NotificationModel;
use App\Models\ProductItemModel;

session_start();
require_once '../models/NotificationModel.php';
require_once '../models/ProductItemModel.php';

header('Content-Type: application/json');

$userData   = $_SESSION['user_data'] ?? [];
$role       = $userData['role'] ?? null;
$department = $userData['department_id'] ?? null;
$userId     = $userData['id'] ?? null;

$alerts = [];
$count  = 0;

// --- SPV Notif ---
if ($role === 'spv') {
    $alerts = NotificationModel::getLatest($department, 'spv');
    $count  = NotificationModel::countUnread($department, 'spv');
}

// --- Admin Notif (stok rendah) ---
if ($role === 'admin') {
    // Ambil notif biasa
    $alerts = NotificationModel::getLatest($userId, 'admin');
    $count  = NotificationModel::countUnread($userId, 'admin');

    // Cek stok rendah
    $lowStockItems = ProductItemModel::getLowStockItems(10);
    foreach ($lowStockItems as $item) {
        // Buat notif baru kalau stok rendah
        NotificationModel::create(
            $userId,
            "Stok produk {$item['name']} tinggal {$item['stock']}, segera kelola!",
            'fas fa-box',
            'danger',
            'stock_alert',
            'admin'
        );
    }
    // Refresh alerts setelah insert
    $alerts = NotificationModel::getLatest($userId, 'admin');
    $count  = NotificationModel::countUnread($userId, 'admin');
}

// --- Customer Notif ---
if ($role === 'customer') {
    $alerts = NotificationModel::getLatest($userId, 'customer');
    $count  = NotificationModel::countUnread($userId, 'customer');
}

// --- Output JSON ---
if (!empty($alerts)) {
    $latest = $alerts[0];
    echo json_encode([
        'new'     => true,
        'id'      => $latest['id'],
        'count'   => $count,
        'message' => $latest['message'],
        'type'    => $latest['color'] ?? 'info',
        'icon'    => $latest['icon'] ?? 'info'
    ]);
} else {
    echo json_encode(['new' => false, 'count' => 0]);
}
