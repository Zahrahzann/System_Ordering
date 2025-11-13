<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class HistoryModel
{
    /**
     * UNTUK ADMIN/SPV: Mengambil riwayat (SEMUA ITEM YANG 'completed')
     */
    public static function getHistoryItems($departmentId = null, $year = null, $month = null)
    {
        $pdo = Database::connect();
        $params = [];
        $sql = "SELECT 
                    i.id as item_id, i.item_name, i.quantity, i.category, i.pic_mfg,
                    i.production_status, i.updated_at as completed_date,
                    o.id as order_id, 
                    c.name as customer_name, c.line,
                    d.name as department_name
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN approvals a ON o.id = a.order_id
                WHERE a.approval_status = 'approve'
                AND i.item_type = 'work_order'
                AND i.production_status = 'completed'"; // <-- Filter Selesai

        if ($departmentId !== null) {
            $sql .= " AND c.department_id = ?";
            $params[] = $departmentId;
        }
        if ($year !== null) {
            $sql .= " AND YEAR(i.updated_at) = ?";
            $params[] = $year;
        }
        if ($month !== null) {
            $sql .= " AND MONTH(i.updated_at) = ?";
            $params[] = $month;
        }
        
        $sql .= " ORDER BY i.updated_at DESC, i.id ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UNTUK CUSTOMER: Mengambil riwayat (SEMUA ITEM 'completed' miliknya)
     */
    public static function getHistoryItemsByCustomer($customerId, $year = null, $month = null)
    {
        $pdo = Database::connect();
        $params = [$customerId];
        $sql = "SELECT 
                    i.id as item_id, i.item_name, i.quantity, i.category, i.material, i.material_type, i.pic_mfg,
                    i.production_status, i.updated_at as completed_date, i.note, i.file_path, i.is_emergency, i.emergency_type,
                    o.id as order_id, 
                    c.name as customer_name, c.line,
                    d.name as department_name
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN approvals a ON o.id = a.order_id
                WHERE o.customer_id = ?
                AND a.approval_status = 'approve'
                AND i.item_type = 'work_order'
                AND i.production_status = 'completed'"; // <-- Filter Selesai

        if ($year !== null) {
            $sql .= " AND YEAR(i.updated_at) = ?";
            $params[] = $year;
        }
        if ($month !== null) {
            $sql .= " AND MONTH(i.updated_at) = ?";
            $params[] = $month;
        }
        
        $sql .= " ORDER BY i.updated_at DESC, i.id ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UNTUK ADMIN: Mengambil data chart bulanan
     */
    public static function getMonthlyChartData($year)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                    MONTH(i.updated_at) as month,
                    COUNT(i.id) as total_items
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN approvals a ON o.id = a.order_id
                WHERE a.approval_status = 'approve'
                AND i.item_type = 'work_order'
                AND i.production_status = 'completed'
                AND YEAR(i.updated_at) = ?
                GROUP BY MONTH(i.updated_at)
                ORDER BY month ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$year]);
        
        $monthlyData = array_fill(1, 12, 0); 
        $dbResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dbResults as $row) {
            $monthlyData[(int)$row['month']] = (int)$row['total_items'];
        }
        
        return $monthlyData;
    }
}