<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class PlantModel {
    public static function getNameById(int $id): ?string {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT name FROM plants WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch (PDO::FETCH_ASSOC);

        return $result['name'] ?? null;
    }
}
