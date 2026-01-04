<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class NotificationModel
{
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

    public static function getLatest($targetKey, $role = 'customer', $limit = 5)
    {
        $pdo = Database::connect();
        $limit = (int) $limit;

        switch ($role) {
            case 'spv':
                $sql = "SELECT id, message, created_at AS date, icon, color, is_read, type
                        FROM notifications 
                        WHERE department = ? AND is_read = 0
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
                        WHERE customer_id = ? AND is_read = 0
                        ORDER BY created_at DESC 
                        LIMIT $limit";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$targetKey]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAsRead($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function markAllRead($targetKey, $role)
    {
        $pdo = Database::connect();
        switch ($role) {
            case 'spv':
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE department = ?");
                break;
            case 'admin':
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
                break;
            default: // customer
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE customer_id = ?");
        }
        $stmt->execute([$targetKey]);
    }

    public static function clearAll($targetKey, $role)
    {
        $pdo = Database::connect();
        switch ($role) {
            case 'spv':
                $stmt = $pdo->prepare("DELETE FROM notifications WHERE department = ?");
                break;
            case 'admin':
                $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
                break;
            default: // customer
                $stmt = $pdo->prepare("DELETE FROM notifications WHERE customer_id = ?");
        }
        $stmt->execute([$targetKey]);
    }

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

    public static function findUnreadByMessage($targetKey, $role, $type, $message)
    {
        $db = Database::connect();
        switch ($role) {
            case 'spv':
                $stmt = $db->prepare("SELECT id FROM notifications WHERE department = ? AND type = ? AND is_read = 0 AND message = ?");
                break;
            case 'admin':
                $stmt = $db->prepare("SELECT id FROM notifications WHERE user_id = ? AND type = ? AND is_read = 0 AND message = ?");
                break;
            default: // customer
                $stmt = $db->prepare("SELECT id FROM notifications WHERE customer_id = ? AND type = ? AND is_read = 0 AND message = ?");
        }
        $stmt->execute([$targetKey, $type, $message]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createUnique(
        $targetKey,
        $message,
        $icon = 'fas fa-info-circle',
        $color = 'primary',
        $type = 'general',
        $role = 'customer'
    ) {
        $pdo = Database::connect();

        // cek apakah notif unread dengan message sama sudah ada
        $existing = self::findUnreadByMessage($targetKey, $role, $type, $message);
        if ($existing) {
            return; // jangan insert duplikat
        }

        self::create($targetKey, $message, $icon, $color, $type, $role);
    }
}
