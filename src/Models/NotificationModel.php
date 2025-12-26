<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class NotificationModel
{

    /**
     * Hitung jumlah notifikasi belum dibaca untuk user sesuai role.
     */
    public static function countUnread($targetKey, $role = 'customer')
    {
        $pdo = Database::connect();

        switch ($role) {
            case 'spv':
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) 
                     FROM notifications 
                     WHERE department = ? AND is_read = 0"
                );
                break;
            case 'admin':
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) 
                     FROM notifications 
                     WHERE user_id = ? AND is_read = 0"
                );
                break;
            default: // customer
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) 
                     FROM notifications 
                     WHERE customer_id = ? AND is_read = 0"
                );
        }

        $stmt->execute([$targetKey]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil notifikasi terbaru untuk user sesuai role.
     */
    public static function getLatest($targetKey, $role = 'customer', $limit = 5)
    {
        $pdo = Database::connect();
        $limit = (int) $limit;

        switch ($role) {
            case 'spv':
                $sql = "SELECT id, message, created_at AS date, icon, color, is_read, type
                        FROM notifications 
                        WHERE department = ? 
                        ORDER BY created_at DESC 
                        LIMIT $limit";
                break;
            case 'admin':
                $sql = "SELECT id, message, created_at AS date, icon, color, is_read, type
                        FROM notifications 
                        WHERE user_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT $limit";
                break;
            default: // customer
                $sql = "SELECT id, message, created_at AS date, icon, color, is_read, type
                        FROM notifications 
                        WHERE customer_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT $limit";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$targetKey]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca.
     */
    public static function markAsRead($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Buat notifikasi baru untuk role tertentu.
     */
    public static function create(
        $targetKey,
        $message,
        $icon = 'fas fa-info-circle',
        $color = 'primary',
        $type = 'general',
        $role = 'customer'
    ) {
        $pdo = Database::connect();

        switch ($role) {
            case 'spv':
                $stmt = $pdo->prepare(
                    "INSERT INTO notifications (department, message, icon, color, type, is_read, created_at) 
                 VALUES (?, ?, ?, ?, ?, 0, NOW())"
                );
                break;
            case 'admin':
                $stmt = $pdo->prepare(
                    "INSERT INTO notifications (user_id, message, icon, color, type, is_read, created_at) 
                 VALUES (?, ?, ?, ?, ?, 0, NOW())"
                );
                break;
            default: // customer
                $stmt = $pdo->prepare(
                    "INSERT INTO notifications (customer_id, message, icon, color, type, is_read, created_at) 
                 VALUES (?, ?, ?, ?, ?, 0, NOW())"
                );
        }

        $stmt->execute([$targetKey, $message, $icon, $color, $type]);
    }
}
