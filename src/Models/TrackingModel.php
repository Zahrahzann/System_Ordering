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
                    i.production_status, -- KOREKSI: Ambil status dari 'items' (i) BUKAN 'orders' (o)
                    o.id as order_id, 
                    c.name as customer_name, 
                    c.line,
                    d.name as department_name,
                    CASE 
                        WHEN i.emergency_type = 'line_stop' THEN 1
                        WHEN i.emergency_type = 'safety' THEN 2
                        ELSE 3 
                    END as priority_level
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN approvals a ON o.id = a.order_id
                WHERE a.approval_status = 'approve'
                AND i.item_type = 'work_order'
                -- KOREKSI: Filter status dari 'items' (i)
                AND i.production_status != 'completed'";

        if ($departmentId !== null) {
            $sql .= " AND c.department_id = :departmentId";
        }
        $sql .= " ORDER BY priority_level ASC, o.created_at ASC, i.id ASC";
        
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
                    i.production_status, -- KOREKSI: Ambil status dari 'items' (i) BUKAN 'orders' (o)
                    o.id as order_id, 
                    c.name as customer_name, 
                    c.line,
                    d.name as department_name,
                    a.approval_status as approval_status, 
                    u.name as spv_name
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                INNER JOIN approvals a ON o.id = a.order_id
                LEFT JOIN users u ON a.spv_id = u.id
                WHERE o.customer_id = ?
                AND i.item_type = 'work_order'
                AND a.approval_status = 'approve'
                -- KOREKSI: Filter status dari 'items' (i)
                AND i.production_status != 'completed'
                ORDER BY o.created_at DESC, i.id ASC"; // KOREKSI: Hapus 'ORDER BY' duplikat

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UNTUK ADMIN: Update PIC dan Status Produksi PER ITEM
     * KOREKSI: Mengupdate tabel 'items' BUKAN 'orders'
     */
    public static function updateItemDetails($itemId, $newStatus, $picMfg)
    {
        $pdo = Database::connect();
        
        // KOREKSI PENTING: Perbarui status produksi di tabel 'items'
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
}