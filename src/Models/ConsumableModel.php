<?php
namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ConsumableModel
{
    // Ambil semua kategori consumable
    public static function getCategories()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    // Ambil produk consumable berdasarkan kategori
    public static function getProductsByCategory($categoryName)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT p.* 
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE c.name = :category
              AND p.item_type = 'Consumable'
        ");
        $stmt->bindParam(':category', $categoryName);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Ambil detail produk consumable
    public static function getProductById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
              AND p.item_type = 'Consumable'
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
