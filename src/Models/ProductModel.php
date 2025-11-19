<?php
namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;

class ProductModel
{
    public static function getAllConsumables()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.item_type = 'Consumable'
            ORDER BY p.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND item_type = 'Consumable'");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function createConsumable($data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, category_id, item_type, image_path)
            VALUES (:name, :description, :price, :category_id, 'Consumable', :image_path)
        ");
        return $stmt->execute($data);
    }

    public static function updateConsumable($id, $data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = :name, description = :description, price = :price, category_id = :category_id, image_path = :image_path
            WHERE id = :id AND item_type = 'Consumable'
        ");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public static function delete($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id AND item_type = 'Consumable'");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
