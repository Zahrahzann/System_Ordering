<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class TrackingModel
{
    /**
     * UNTUK ADMIN & SPV: Mengambil semua ITEM work order yang AKTIF (BELUM SELESAI).
     * Catatan: hasil tetap per item, grouping per order_id dilakukan di controller.
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
                i.estimasi_pengerjaan,
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
            WHERE o.approval_status = 'approve'
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
            i.estimasi_pengerjaan,
            i.production_status,
            i.is_emergency,
            i.emergency_type,
            o.id as order_id, 
            o.approval_status,
            c.name as customer_name, 
            c.line,
            d.name as department_name
        FROM items i
        JOIN orders o ON i.order_id = o.id
        JOIN customers c ON o.customer_id = c.id
        JOIN departments d ON c.department_id = d.id
        WHERE o.customer_id = ?
          AND i.item_type = 'work_order'
          AND o.approval_status = 'approve'
          AND i.production_status != 'completed'
        ORDER BY o.created_at DESC, i.id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update PIC dan Status Produksi PER ITEM
     */
    public static function updateItemDetails($itemId, $newStatus, $picMfg, $estimasi = null)
    {
        $pdo = Database::connect();
        $sql = "UPDATE items 
            SET 
                pic_mfg = ?, 
                production_status = ?, 
                estimasi_pengerjaan = ? 
            WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$picMfg, $newStatus, $estimasi, $itemId]);
    }

    /**
     * Mendapatkan statistik dashboard berdasarkan role
     */
    public static function getDashboardStats($role, $userId = null)
    {
        $pdo = Database::connect();

        $sql = "SELECT 
            CASE 
                WHEN i.production_status IS NULL OR i.production_status = '' THEN 'pending'
                ELSE i.production_status
            END AS production_status,
            COUNT(DISTINCT i.order_id) as count
        FROM items i
        JOIN orders o ON i.order_id = o.id
        JOIN customers c ON o.customer_id = c.id
        WHERE i.item_type = 'work_order'
          AND o.approval_status = 'approve'
          AND i.production_status != 'completed'";

        if ($role === 'customer') {
            $sql .= " AND o.customer_id = :userId";
        } elseif ($role === 'spv') {
            $sql .= " AND c.department_id = (
                SELECT department_id FROM users WHERE id = :userId
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
            } else {
                $stats[$status] = $count; // fallback untuk status baru
            }
        }

        return $stats;
    }

    /**
     * Cari order berdasarkan item_id
     */
    public static function findItemById($itemId)
    {
        $pdo = Database::connect();
        $sql = "SELECT i.*, o.department AS department_id
            FROM items i
            JOIN orders o ON i.order_id = o.id
            WHERE i.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update durasi aktual pengerjaan di tabel items
     */
    public static function updateItemDuration($itemId, $durationMinutes)
    {
        $pdo = Database::connect();
        $sql = "UPDATE items 
            SET actual_duration_minutes = :duration 
            WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'duration' => $durationMinutes,
            'id'       => $itemId
        ]);
    }
}
