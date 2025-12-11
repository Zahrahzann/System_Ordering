<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ConsumHistoryModel
{
    public static function getHistoryFiltered(string $role, array $filters): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::connect();

        $sql = "
            SELECT 
                o.id,
                o.order_code,
                o.created_at,
                o.status,
                o.quantity,
                o.price,
                p.name AS product_name,
                p.image_path AS product_image,
                p.file_path AS drawing_file,
                p.item_code,
                pt.name AS product_type_name,
                s.name AS section_name,
                c.name AS customer_name,
                d.name AS department,
                c.line
            FROM consum_orders o
            JOIN product_items p   ON o.product_item_id = p.id
            JOIN product_types pt  ON p.product_type_id = pt.id
            JOIN sections s        ON pt.section_id = s.id
            JOIN customers c       ON o.customer_id = c.id
            LEFT JOIN departments d ON c.department_id = d.id
            WHERE o.status = 'Selesai'
        ";

        $params = [];

        // text search
        if (!empty($filters['q'])) {
            $sql .= " AND (p.name LIKE ? OR p.item_code LIKE ?)";
            $params[] = "%{$filters['q']}%";
            $params[] = "%{$filters['q']}%";
        }

        // filter by section/type/item
        if (!empty($filters['section'])) {
            $sql .= " AND s.id = ?";
            $params[] = (int)$filters['section'];
        }
        if (!empty($filters['type'])) {
            $sql .= " AND pt.id = ?";
            $params[] = (int)$filters['type'];
        }
        if (!empty($filters['item'])) {
            $sql .= " AND p.id = ?";
            $params[] = (int)$filters['item'];
        }

        // role-based scope
        if ($role === 'customer') {
            $sql .= " AND c.id = ?";
            $params[] = $_SESSION['user_data']['id'] ?? 0;
        } elseif ($role === 'spv') {
            $sql .= " AND c.department_id = ?";
            $params[] = $_SESSION['user_data']['department_id'] ?? 0;
        }

        // extra filters for admin/spv
        if ($role !== 'customer') {
            if (!empty($filters['customer'])) {
                $sql .= " AND c.name LIKE ?";
                $params[] = "%{$filters['customer']}%";
            }
            if (!empty($filters['department'])) {
                $sql .= " AND d.name LIKE ?";
                $params[] = "%{$filters['department']}%";
            }
            if (!empty($filters['line'])) {
                $sql .= " AND c.line LIKE ?";
                $params[] = "%{$filters['line']}%";
            }
        }

        // filter bulan/tahun
        if (!empty($filters['month'])) {
            $sql .= " AND MONTH(o.created_at) = ?";
            $params[] = (int)$filters['month'];
        }
        if (!empty($filters['year'])) {
            $sql .= " AND YEAR(o.created_at) = ?";
            $params[] = (int)$filters['year'];
        }

        // order by created_at 
        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // dropdown helpers
    public static function getAllSections(): array
    {
        $pdo = Database::connect();
        return $pdo->query("SELECT id, name FROM sections ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllProductTypes(?int $sectionId = null): array
    {
        $pdo = Database::connect();
        if ($sectionId) {
            $stmt = $pdo->prepare("SELECT id, name FROM product_types WHERE section_id = ? ORDER BY name ASC");
            $stmt->execute([$sectionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pdo->query("SELECT id, name FROM product_types ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllProductItems(?int $typeId = null): array
    {
        $pdo = Database::connect();
        if ($typeId) {
            $stmt = $pdo->prepare("SELECT id, name FROM product_items WHERE product_type_id = ? ORDER BY name ASC");
            $stmt->execute([$typeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pdo->query("SELECT id, name FROM product_items ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllDepartments(): array
    {
        $pdo = Database::connect();
        return $pdo->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
