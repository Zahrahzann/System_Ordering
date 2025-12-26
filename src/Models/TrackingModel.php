<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class TrackingModel
{
    /**
     * UNTUK ADMIN & SPV: Mengambil semua ITEM work order yang AKTIF (BELUM SELESAI).
     */
    public static function getApprovedItemsByPriority($departmentId = null)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                    i.id as item_id, 
                    i.item_name, 
                    i.quantity, 
                    i.category, 
                    i.pic_mfg,
                    i.is_emergency,
                    i.emergency_type,
                    i.production_status, 
                    o.id as order_id, 
                    c.name as customer_name, 
                    c.line,
                    d.name as department_name,
                    CASE 
                        WHEN i.emergency_type = 'line_stop' THEN 1
                        WHEN i.emergency_type = 'safety' THEN 2
                        ELSE 3 
                    END as priority_level,
                    CASE 
                        WHEN i.production_status = 'finish' THEN 1
                        WHEN i.production_status = 'on_progress' THEN 2
                        WHEN i.production_status = 'pending' THEN 3
                        ELSE 4
                    END as status_order
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN approvals a ON o.id = a.order_id
                WHERE a.approval_status = 'approve'
                AND i.item_type = 'work_order'
                AND i.production_status != 'completed'";

        if ($departmentId !== null) {
            $sql .= " AND c.department_id = :departmentId";
        }
        $sql .= " ORDER BY status_order ASC, priority_level ASC, o.created_at ASC, i.id ASC";

        $stmt = $pdo->prepare($sql);
        if ($departmentId !== null) {
            $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UNTUK CUSTOMER: Mengambil semua ITEM miliknya yang AKTIF (BELUM SELESAI).
     */
    public static function getItemsByCustomer($customerId)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                i.id as item_id, 
                i.item_name, 
                i.quantity, 
                i.category, 
                i.pic_mfg,
                i.production_status,
                i.is_emergency,
                i.emergency_type,
                o.id as order_id, 
                c.name as customer_name, 
                c.line,
                d.name as department_name,
                COALESCE(a.approval_status, o.approval_status) as approval_status, 
                u.name as spv_name
            FROM items i
            JOIN orders o ON i.order_id = o.id
            JOIN customers c ON o.customer_id = c.id
            JOIN departments d ON c.department_id = d.id
            LEFT JOIN approvals a 
                   ON o.id = a.order_id
                  AND a.updated_at = (
                      SELECT MAX(updated_at) 
                      FROM approvals 
                      WHERE order_id = o.id
                  )
            LEFT JOIN users u ON a.spv_id = u.id
            WHERE o.customer_id = ?
              AND i.item_type = 'work_order'
              AND (o.approval_status = 'approve' OR a.approval_status = 'approve')
              AND i.production_status != 'completed'
            ORDER BY o.created_at DESC, i.id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update PIC dan Status Produksi PER ITEM
     * KOREKSI: Mengupdate tabel 'items' BUKAN 'orders' 
     */
    public static function updateItemDetails($itemId, $newStatus, $picMfg)
    {
        $pdo = Database::connect();

        // Perbarui status produksi di tabel 'items'
        $sql = "UPDATE items 
                SET 
                    pic_mfg = ?, 
                    production_status = ? 
                WHERE 
                    id = ?";

        $stmt = $pdo->prepare($sql);
        // KOREKSI URUTAN: Sesuaikan urutan parameter dengan '?'
        return $stmt->execute([$picMfg, $newStatus, $itemId]);
    }

    public static function getDashboardStats($role, $userId = null)
    {
        $pdo = Database::connect();

        $sql = "SELECT production_status, COUNT(*) as count
            FROM items i
            JOIN orders o ON i.order_id = o.id
            JOIN approvals a ON o.id = a.order_id
            WHERE i.item_type = 'work_order'
            AND a.approval_status = 'approve'
            AND i.production_status != 'completed'";

        if ($role === 'customer') {
            $sql .= " AND o.customer_id = :userId";
        } elseif ($role === 'spv') {
            $sql .= " AND c.department_id = (
                      SELECT department_id FROM customers WHERE id = :userId
                  )";
        }

        $sql .= " GROUP BY production_status";

        $stmt = $pdo->prepare($sql);
        if ($role !== 'admin') {
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Normalisasi hasil
        $stats = [
            'total'       => 0,
            'pending'     => 0,
            'on_progress' => 0,
            'finish'      => 0
        ];

        foreach ($rows as $row) {
            $status = $row['production_status'];
            $count  = (int)$row['count'];
            $stats['total'] += $count;
            if (isset($stats[$status])) {
                $stats[$status] = $count;
            }
        }

        return $stats;
    }
}
