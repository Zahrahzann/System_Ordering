<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\NotificationModel;

// Set header JSON
header('Content-Type: application/json');

$userData   = $_SESSION['user_data'] ?? [];
$role       = $userData['role'] ?? null;
$department = $userData['department_id'] ?? null;
$userId     = $userData['id'] ?? null;

$response = ['new' => false];

try {
    // SPV
    if ($role === 'spv' && $department) {
        $alert = NotificationModel::getLatest($department, 'spv');

        if ($alert) {
            $response = [
                'new'     => true,
                'id'      => $alert['id'],
                'message' => nl2br($alert['message']),
                'type'    => $alert['color'] ?? 'info',
                'icon'    => $alert['icon'] ?? 'fas fa-bell',
                'date'    => $alert['date'],
                'kind'    => $alert['type'] ?? 'general'
            ];
        }
    }

    // Admin
    elseif ($role === 'admin' && $userId) {
        $alert = NotificationModel::getLatest($userId, 'admin');

        if ($alert) {
            $response = [
                'new'     => true,
                'id'      => $alert['id'],
                'message' => nl2br($alert['message']),
                'type'    => $alert['color'] ?? 'info',
                'icon'    => $alert['icon'] ?? 'fas fa-bell',
                'date'    => $alert['date'],
                'kind'    => $alert['type'] ?? 'general'
            ];
        }
    }

    // Customer
    elseif ($role === 'customer' && $userId) {
        $alert = NotificationModel::getLatest($userId, 'customer');

        if ($alert) {
            $response = [
                'new'      => true,
                'id'       => $alert['id'],
                'message'  => nl2br($alert['message']),
                'type'     => $alert['color'] ?? 'info',
                'icon'     => $alert['icon'] ?? 'fas fa-bell',
                'date'     => $alert['date'],
                'kind'     => $alert['type'] ?? 'general',
                'order_id' => $alert['order_id'] ?? null
            ];
        }
    }

    error_log("notifications.php - Response for role $role: " . json_encode($response));
} catch (Exception $e) {
    error_log("notifications.php error: " . $e->getMessage());
    $response = [
        'new' => false,
        'error' => $e->getMessage()
    ];
}

echo json_encode($response);
exit;
