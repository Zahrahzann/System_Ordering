<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/NotificationModel.php';

use App\Models\NotificationModel;

// Set header JSON
header('Content-Type: application/json');

// Ambil input JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log untuk debugging
error_log("mark_notification.php - Raw input: " . $input);

$notifId = isset($data['notif_id']) ? (int)$data['notif_id'] : null;

if ($notifId && $notifId > 0) {
    try {
        $updated = NotificationModel::markAsRead($notifId);

        if ($updated) {
            error_log("Notif $notifId berhasil ditandai read");
            echo json_encode([
                'success' => true,
                'status'  => 'success',
                'notifId' => $notifId
            ]);
        } else {
            error_log("Notif $notifId tidak ditemukan / tidak diupdate");
            echo json_encode([
                'success' => false,
                'status'  => 'error',
                'message' => 'Notif tidak ditemukan'
            ]);
        }
    } catch (Exception $e) {
        error_log("Gagal update notif $notifId: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'status'  => 'error',
            'message' => 'Database update gagal: ' . $e->getMessage()
        ]);
    }
} else {
    error_log("notif_id missing atau invalid: " . var_export($data, true));
    echo json_encode([
        'success' => false,
        'status'  => 'error',
        'message' => 'notif_id missing atau invalid'
    ]);
}
exit;
