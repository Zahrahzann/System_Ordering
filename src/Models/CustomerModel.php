<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class CustomerModel
{

    public static function findByNpkAndName(string $npk, string $name): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE npk = ? AND name = ?");
        $stmt->execute([$npk, $name]);
        $customer = $stmt->fetch();

        return $customer ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO customers(name, npk, phone, plant_id, department_id, line) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['npk'],
            $data['phone'],
            $data['plant_id'],     
            $data['department_id'], 
            $data['line']
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch();

        return $customer ?: null;
    }

    public static function findByNpk(string $npk): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE npk = ?");
        $stmt->execute([$npk]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        return $customer ?: null;
    }
}
