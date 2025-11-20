<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ApprovalModel
{
    /**
     * Ambil semua order untuk SPV (waiting + reject).
     */
    public static function getOrdersForSpv($spvId)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                    o.id as order_id, 
                    o.created_at, 
                    c.name as customer_name, 
                    d.name as department_name, 
                    a.approval_status
                FROM approvals a
                JOIN orders o ON a.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                WHERE a.spv_id = ?
                  AND a.approval_status IN ('waiting','reject')
                ORDER BY o.created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$spvId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hitung jumlah order berdasarkan status untuk SPV.
     */
    public static function countByStatusForSpv($spvId, $status)
    {
        $pdo = Database::connect();
        $sql = "SELECT COUNT(*) FROM approvals WHERE spv_id = ? AND approval_status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$spvId, $status]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil detail order by ID.
     */
    public static function findOrderById($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT o.*, 
                       c.name as customer_name, 
                       c.npk as customer_npk, 
                       c.line, 
                       d.name as department_name, 
                       p.name as plant_name
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN plants p ON o.plant_id = p.id
                WHERE o.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua item dari order.
     */
    public static function findOrderItemsByOrderId($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT * FROM items WHERE order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update status approval (approve/reject) + sinkron ke tabel orders.
     */
    public static function updateApprovalStatus($orderId, $spvId, $status, $notes)
    {
        $pdo = Database::connect();
        $sql = "UPDATE approvals 
                   SET approval_status = ?, comments = ?, updated_at = NOW()
                 WHERE order_id = ? AND spv_id = ?";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$status, $notes, $orderId, $spvId]);

        if ($ok) {
            self::updateOrderStatus($orderId, $status);
        }
        return $ok;
    }

    /**
     * Update status di tabel orders.
     */
    public static function updateOrderStatus($orderId, $status)
    {
        $pdo = Database::connect();
        $sql = "UPDATE orders SET approval_status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$status, $orderId]);
    }

    /**
     * Ambil data approval by order.
     */
    public static function findApprovalByOrderId($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                    a.approval_status, 
                    a.comments, 
                    a.updated_at,
                    u.name as spv_name
                FROM approvals a
                LEFT JOIN users u ON a.spv_id = u.id
                WHERE a.order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
