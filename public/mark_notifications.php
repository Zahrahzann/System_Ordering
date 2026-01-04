<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/NotificationModel.php';

use App\Models\NotificationModel;

$userData = $_SESSION['user_data'] ?? [];
$userId   = $userData['id'] ?? null;
$role     = $userData['role'] ?? null;

if ($userId && $role) {
    NotificationModel::markAllRead($userId, $role);
    echo json_encode(['success' => true, 'role' => $role]);
} else {
    echo json_encode(['success' => false]);
}
