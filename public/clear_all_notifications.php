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
    $deleted = 0;

    if ($role === 'customer' && $userId) {
        $deleted = NotificationModel::clearAll($userId, 'customer');
    } elseif ($role === 'spv' && $department) {
        $deleted = NotificationModel::clearAll($department, 'spv');
    } elseif ($role === 'admin' && $userId) {
        $deleted = NotificationModel::clearAll($userId, 'admin');
    }

    if ($deleted > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Berhasil menghapus $deleted notifikasi",
            'count' => $deleted
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Tidak ada notifikasi untuk dihapus',
            'count' => 0
        ]);
    }
} catch (Exception $e) {
    error_log("clear_all_notifications error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
exit;
