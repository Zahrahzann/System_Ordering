<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ProductModel
{
    public static function getConsumableByCategory($category)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
        SELECT p.* 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE c.name = :category
        AND p.item_type = 'Consumable'
    ");
        $stmt->bindParam(':category', $category);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
