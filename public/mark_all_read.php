<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/NotificationModel.php';

use App\Models\NotificationModel;

header('Content-Type: application/json');

$userData = $_SESSION['user_data'] ?? [];
$role     = $userData['role'] ?? null;
$userId   = $userData['id'] ?? null;
$department = $userData['department_id'] ?? null;

try {
    $updated = 0;

    if ($role === 'customer' && $userId) {
        $updated = NotificationModel::markAllRead($userId, 'customer');
    } elseif ($role === 'spv' && $department) {
        $updated = NotificationModel::markAllRead($department, 'spv');
    } elseif ($role === 'admin' && $userId) {
        $updated = NotificationModel::markAllRead($userId, 'admin');
    }

    if ($updated > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Berhasil menandai $updated notifikasi sebagai dibaca",
            'count' => $updated
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Tidak ada notifikasi yang perlu ditandai',
            'count' => 0
        ]);
    }
} catch (Exception $e) {
    error_log("mark_all_read error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
exit;
