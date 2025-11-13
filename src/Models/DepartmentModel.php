<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class DepartmentModel {
    public static function getNameById(int $id): ?string {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT name FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch (PDO::FETCH_ASSOC);

        return $result['name'] ?? null;
    }
}