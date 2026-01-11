<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class HistoryModel
{
    public static function getAllDepartments()
    {
        try {
            $pdo = Database::connect();
            $sql = "SELECT id, name FROM departments ORDER BY name ASC";
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            echo "<pre>Error getAllDepartments: " . $e->getMessage() . "</pre>";
            return [];
        }
    }
    public static function getHistoryItems($departmentId = null, $year = null, $month = null)
    {
        try {
            $pdo = Database::connect();
            $params = [];
            $sql = "SELECT i.id AS item_id, i.item_name, i.quantity, i.category, i.pic_mfg, i.production_status, i.updated_at AS completed_date, i.needed_date, i.note, i.file_path, md.dimension AS material_dimension, mt.name AS material_type, mt.material_number, u.name AS spv_name, o.id AS order_id, c.name AS customer_name, c.line, d.name AS department_name FROM items i JOIN orders o ON i.order_id = o.id JOIN customers c ON o.customer_id = c.id JOIN departments d ON c.department_id = d.id JOIN approvals a ON o.id = a.order_id LEFT JOIN users u ON a.spv_id = u.id LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id LEFT JOIN material_types mt ON md.material_type_id = mt.id WHERE a.approval_status = 'approve' AND i.item_type = 'work_order' AND i.production_status = 'completed'";
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
        } catch (\Exception $e) {
            echo "<pre>Error getHistoryItems: " . $e->getMessage() . "</pre>";
            return [];
        }
    }
    public static function getHistoryItemsByCustomer($customerId, $year = null, $month = null)
    {
        if (!$customerId) return [];
        try {
            $pdo = Database::connect();
            $params = [$customerId];
            $sql = "SELECT i.id AS item_id, i.item_name, i.quantity, i.category, i.pic_mfg, i.production_status, i.updated_at AS completed_date, i.needed_date, i.note, i.file_path, i.is_emergency, i.emergency_type, md.dimension AS material_dimension, mt.material_number, mt.name AS material_type, u.name AS spv_name, o.id AS order_id, c.name AS customer_name, c.line, dpt.name AS department_name FROM items i JOIN orders o ON i.order_id = o.id JOIN customers c ON o.customer_id = c.id JOIN departments dpt ON c.department_id = dpt.id JOIN approvals a ON o.id = a.order_id LEFT JOIN users u ON a.spv_id = u.id LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id LEFT JOIN material_types mt ON md.material_type_id = mt.id WHERE o.customer_id = ? AND a.approval_status = 'approve' AND i.item_type = 'work_order' AND i.production_status = 'completed'";
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
        } catch (\Exception $e) {
            echo "<pre>Error getHistoryItemsByCustomer: " . $e->getMessage() . "</pre>";
            return [];
        }
    }
    public static function findItemById($itemId)
    {
        try {
            $pdo = Database::connect();
            $sql = "SELECT i.*, o.customer_id, md.dimension AS material_dimension, mt.name AS material_type, mt.material_number, u.name AS spv_name FROM items i JOIN orders o ON i.order_id = o.id JOIN approvals a ON o.id = a.order_id LEFT JOIN users u ON a.spv_id = u.id LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id LEFT JOIN material_types mt ON md.material_type_id = mt.id WHERE i.id = ? AND i.item_type = 'work_order'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$itemId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            echo "<pre>Error findItemById: " . $e->getMessage() . "</pre>";
            return null;
        }
    }
    public static function addItemToCart($customerId, $item)
    {
        try {
            $pdo = Database::connect();
            $sql = "INSERT INTO items (customer_id, item_name, quantity, category, material_status, material_dimension_id, needed_date, file_path, item_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'work_order')";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$customerId, $item['item_name'] ?? '', $item['quantity'] ?? 0, $item['category'] ?? '', $item['material_status'] ?? '', $item['material_dimension_id'] ?? null, $item['needed_date'] ?? null, $item['file_path'] ?? null]);
        } catch (\Exception $e) {
            echo "<pre>Error addItemToCart: " . $e->getMessage() . "</pre>";
            return false;
        }
    }

    public static function getMonthlyChartData($year)
    {
        try {
            $pdo = Database::connect();
            $sql = "SELECT 
                        MONTH(i.updated_at) AS month,
                        COUNT(i.id) AS total_items
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

            // Inisialisasi array 12 bulan dengan nilai 0
            $monthlyData = array_fill(1, 12, 0);

            $dbResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbResults as $row) {
                $monthIndex = (int)$row['month'];
                $monthlyData[$monthIndex] = (int)$row['total_items'];
            }

            return $monthlyData;
        } catch (\Exception $e) {
            echo "<pre>Error getMonthlyChartData: " . $e->getMessage() . "</pre>";
            return array_fill(1, 12, 0);
        }
    }
}
