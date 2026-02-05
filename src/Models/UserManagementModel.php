<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class UserManagementModel
{
    public static function getAllSpv($departmentId = null, $searchQuery = null)
    {
        $pdo = Database::connect();
        $params = [];

        $sql = "SELECT 
                    u.id, u.name, u.npk, u.email, u.phone, 
                    d.name as department_name, 
                    p.name as plant_name
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.id
                LEFT JOIN plants p ON u.plant_id = p.id
                WHERE u.role = 'spv'";

        if (!empty($departmentId)) {
            $sql .= " AND u.department_id = ?";
            $params[] = $departmentId;
        }

        if (!empty($searchQuery)) {
            $sql .= " AND (u.name LIKE ? OR u.npk LIKE ?)";
            $searchTerm = '%' . $searchQuery . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY u.name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getAllCustomers($departmentId = null, $searchQuery = null)
    {
        $pdo = Database::connect();
        $params = [];

        $sql = "SELECT 
                    c.id, c.name, c.npk, c.phone, c.line,
                    d.name as department_name, 
                    p.name as plant_name
                FROM customers c
                LEFT JOIN departments d ON c.department_id = d.id
                LEFT JOIN plants p ON c.plant_id = p.id
                WHERE 1=1";

        if (!empty($departmentId)) {
            $sql .= " AND c.department_id = ?";
            $params[] = $departmentId;
        }

        if (!empty($searchQuery)) {
            $sql .= " AND (c.name LIKE ? OR c.npk LIKE ? OR c.line LIKE ?)";
            $searchTerm = '%' . $searchQuery . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY c.name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fungsi ini sudah benar (tidak diubah)
     */
    public static function getAllDepartments()
    {
        $pdo = Database::connect();
        $sql = "SELECT id, name FROM departments ORDER BY name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteCustomerById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public static function deleteSpvById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'spv'");
        return $stmt->execute([$id]);
    }
}
