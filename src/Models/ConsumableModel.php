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
            JOIN sections s ON p.section_id = s.id
            WHERE s.name = :section
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
            SELECT p.*, s.name AS section_name
            FROM products p
            JOIN sections s ON p.section_id = s.id
            WHERE p.id = :id
              AND p.item_type = 'Consumable'
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Ambil satu section berdasarkan ID
    public static function getSectionById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM sections WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
