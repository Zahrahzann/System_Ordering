<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/NotificationModel.php';

use App\Models\NotificationModel;

$userData   = $_SESSION['user_data'] ?? [];
$userId     = $userData['id'] ?? null;
$role       = $userData['role'] ?? null;
$department = $userData['department_id'] ?? null;

if ($role) {
    if ($role === 'spv' && $department) {
        NotificationModel::clearAll($department, 'spv');
    } elseif ($role === 'admin' && $userId) {
        NotificationModel::clearAll($userId, 'admin');
    } elseif ($role === 'customer' && $userId) {
        NotificationModel::clearAll($userId, 'customer');
    }

    echo json_encode(['success' => true, 'role' => $role]);
} else {
    echo json_encode(['success' => false]);
}
