<?php
namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ConsumableModel
{
    // Mengambil semua Section consumable
    public static function getSection()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM sections ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    // Ambil produk consumable berdasarkan Section
    public static function getProductsBySection($sectionName)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT p.* 
            FROM products p
            JOIN sections c ON p.section_id = c.id
            WHERE c.name = :section
              AND p.item_type = 'Consumable'
        ");
        $stmt->bindParam(':section', $sectionName);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Ambil detail produk consumable
    public static function getProductById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS section_name
            FROM products p
            JOIN sections c ON p.section_id = c.id
            WHERE p.id = :id
              AND p.item_type = 'Consumable'
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
