<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class NotificationModel
{
    public static function countUnread($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM notifications 
             WHERE customer_id = ? AND is_read = 0"
        );
        $stmt->execute([$customerId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getLatest($customerId, $limit = 5)
    {
        $pdo = Database::connect();
        $limit = (int) $limit; // pastikan integer

        $sql = "SELECT id, message, created_at AS date, icon, color, is_read
                FROM notifications 
                WHERE customer_id = ? 
                ORDER BY created_at DESC 
                LIMIT $limit";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAsRead($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function create($targetId, $message, $icon = 'fas fa-info-circle', $color = 'primary', $type = 'general', $role = 'customer')
    {
        $pdo = Database::connect();

        if ($role === 'admin') {
            $stmt = $pdo->prepare(
                "INSERT INTO notifications (user_id, message, icon, color, type) 
             VALUES (?, ?, ?, ?, ?)"
            );
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO notifications (customer_id, message, icon, color, type) 
             VALUES (?, ?, ?, ?, ?)"
            );
        }

        $stmt->execute([$targetId, $message, $icon, $color, $type]);
    }
}
