<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class WOReportModel
{
    /**
     * Ambil data Work Order completed sesuai filter tahun/bulan.
     * @param int|null $year
     * @param int|null $month
     * @return array
     */
    public static function getReportData($year = null, $month = null)
    {
        $pdo = Database::connect();
        $params = [];
        $sql = "SELECT 
                    i.id as item_id,
                    i.item_name,
                    i.quantity,
                    i.production_status,
                    i.updated_at as completed_date,
                    o.id as order_id,
                    c.name as customer_name,
                    d.name as department_name
                FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN approvals a ON o.id = a.order_id
                WHERE a.approval_status = 'approve'
                AND i.item_type = 'work_order'
                AND i.production_status = 'completed'";

        if ($year !== null) {
            $sql .= " AND YEAR(i.updated_at) = ?";
            $params[] = $year;
        }
        if ($month !== null) {
            $sql .= " AND MONTH(i.updated_at) = ?";
            $params[] = $month;
        }

        $sql .= " ORDER BY i.updated_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
