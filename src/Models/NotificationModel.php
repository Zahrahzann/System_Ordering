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
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE department = ? AND is_read = 0");
                break;
            case 'admin':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                break;
            default: // customer
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE customer_id = ? AND is_read = 0");
        }

        $stmt->execute([$targetKey]);
        return (int) $stmt->fetchColumn();
    }

    public static function getLatest($targetKey, $role = 'customer')
    {
        $pdo = Database::connect();
        switch ($role) {
            case 'spv':
                $sql = "SELECT id, message, created_at AS date, icon, color, type, order_id
                    FROM notifications 
                    WHERE department = ? AND is_read = 0
                    ORDER BY created_at DESC LIMIT 1";
                break;
            case 'admin':
                $sql = "SELECT id, message, created_at AS date, icon, color, type, order_id
                    FROM notifications 
                    WHERE user_id = ? AND is_read = 0
                    ORDER BY created_at DESC LIMIT 1";
                break;
            default: // customer
                $sql = "SELECT id, message, created_at AS date, icon, color, type, order_id
                    FROM notifications 
                    WHERE customer_id = ? AND is_read = 0
                    ORDER BY created_at DESC LIMIT 1";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$targetKey]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getUnreadList($targetKey, $role = 'customer', $limit = 10)
    {
        $pdo = Database::connect();
        switch ($role) {
            case 'spv':
                $sql = "SELECT id, message, created_at AS date, icon, color, type
                        FROM notifications 
                        WHERE department = ? AND is_read = 0
                        ORDER BY created_at DESC LIMIT ?";
                break;
            case 'admin':
                $sql = "SELECT id, message, created_at AS date, icon, color, type
                        FROM notifications 
                        WHERE user_id = ? AND is_read = 0
                        ORDER BY created_at DESC LIMIT ?";
                break;
            default: // customer
                $sql = "SELECT id, message, created_at AS date, icon, color, type
                        FROM notifications 
                        WHERE customer_id = ? AND is_read = 0
                        ORDER BY created_at DESC LIMIT ?";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $targetKey, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAsRead($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0; // balikin true kalau ada baris yang kena update
    }

    public static function markAllRead($targetKey, $role)
    {
        $pdo = Database::connect();
        switch ($role) {
            case 'spv':
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE department = ? AND is_read = 0");
                break;
            case 'admin':
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
                break;
            default: // customer
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE customer_id = ? AND is_read = 0");
        }
        $stmt->execute([$targetKey]);
        return $stmt->rowCount();
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
        return $stmt->rowCount();
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

        $stmt->execute([$targetKey, trim($message), $icon, $color, $type]);
        return $pdo->lastInsertId();
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
        $stmt->execute([$targetKey, $type, trim($message)]);
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

        $normalizedMessage = trim(preg_replace('/\s+/', ' ', $message));
        $existing = self::findUnreadByMessage($targetKey, $role, $type, $normalizedMessage);
        if ($existing) {
            return false; // jangan insert duplikat
        }

        return self::create($targetKey, $normalizedMessage, $icon, $color, $type, $role);
    }

    public static function createWithOrderId(
        $targetKey,
        $message,
        $icon = 'fas fa-info-circle',
        $color = 'primary',
        $type = 'general',
        $role = 'customer',
        $orderId = null
    ) {
        $pdo = Database::connect();

        switch ($role) {
            case 'spv':
                $stmt = $pdo->prepare(
                    "INSERT INTO notifications (department, message, icon, color, type, order_id, is_read, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 0, NOW())"
                );
                $stmt->execute([$targetKey, trim($message), $icon, $color, $type, $orderId]);
                break;

            case 'admin':
                $stmt = $pdo->prepare(
                    "INSERT INTO notifications (user_id, message, icon, color, type, order_id, is_read, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 0, NOW())"
                );
                $stmt->execute([$targetKey, trim($message), $icon, $color, $type, $orderId]);
                break;

            default: // customer
                $stmt = $pdo->prepare(
                    "INSERT INTO notifications (customer_id, message, icon, color, type, order_id, is_read, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 0, NOW())"
                );
                $stmt->execute([$targetKey, trim($message), $icon, $color, $type, $orderId]);
                break;
        }

        return $pdo->lastInsertId();
    }
}
