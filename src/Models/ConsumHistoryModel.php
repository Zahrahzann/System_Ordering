<?php
namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ConsumHistoryModel
{
    public static function getHistoryFiltered(string $role, array $filters): array
    {
        $pdo = Database::connect();
        $sql = "SELECT * FROM consum_orders WHERE status = 'Selesai'";
        $params = [];

        if ($filters['q']) {
            $sql .= " AND (product_name LIKE ? OR item_code LIKE ?)";
            $params[] = "%{$filters['q']}%";
            $params[] = "%{$filters['q']}%";
        }
        if ($filters['section']) {
            $sql .= " AND section_id = ?";
            $params[] = $filters['section'];
        }
        if ($filters['type']) {
            $sql .= " AND product_type_id = ?";
            $params[] = $filters['type'];
        }
        if ($filters['item']) {
            $sql .= " AND product_item_id = ?";
            $params[] = $filters['item'];
        }

        if ($role === 'customer') {
            $sql .= " AND customer_id = ?";
            $params[] = $_SESSION['user_data']['id'] ?? 0;
        } elseif ($role === 'spv') {
            $sql .= " AND department_id = ?";
            $params[] = $_SESSION['user_data']['department_id'] ?? 0;
        }

        if ($role !== 'customer') {
            if ($filters['customer']) {
                $sql .= " AND customer_name LIKE ?";
                $params[] = "%{$filters['customer']}%";
            }
            if ($filters['department']) {
                $sql .= " AND department LIKE ?";
                $params[] = "%{$filters['department']}%";
            }
            if ($filters['line']) {
                $sql .= " AND line LIKE ?";
                $params[] = "%{$filters['line']}%";
            }
        }

        $sql .= " ORDER BY updated_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // dropdown helpers sama seperti di ConsumOrderModel
    // public static function getAllSections(): array { /* ... */ }
    // public static function getAllProductTypes(?int $sectionId = null): array { /* ... */ }
    // public static function getAllProductItems(?int $typeId = null): array { /* ... */ }
}
