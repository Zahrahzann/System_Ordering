<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class UserModel
{
    public static function getAllAdmins(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'admin'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
