<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class MaterialDimensionModel
{
    public static function getAllGrouped()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("
            SELECT md.*, mt.material_number, mt.name
            FROM material_dimensions md
            JOIN material_types mt ON md.material_type_id = mt.id
            ORDER BY mt.name ASC, md.dimension ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT md.*, mt.material_number, mt.name
            FROM material_dimensions md
            JOIN material_types mt ON md.material_type_id = mt.id
            WHERE md.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            INSERT INTO material_dimensions (material_type_id, dimension, stock)
            VALUES (:material_type_id, :dimension, :stock)
        ");
        $stmt->bindParam(':material_type_id', $data['material_type_id']);
        $stmt->bindParam(':dimension', $data['dimension']);
        $stmt->bindParam(':stock', $data['stock']);
        return $stmt->execute();
    }

    public static function update($id, $data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            UPDATE material_dimensions
            SET dimension = :dimension, stock = :stock
            WHERE id = :id
        ");
        $stmt->bindParam(':dimension', $data['dimension']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM material_dimensions WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Hapus semua dimension berdasarkan type */
    public static function deleteByType($typeId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM material_dimensions WHERE material_type_id = :typeId");
        $stmt->bindParam(':typeId', $typeId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function getByType($typeId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT id, dimension, stock 
            FROM material_dimensions 
            WHERE material_type_id = ? 
            ORDER BY dimension ASC
        ");
        $stmt->execute([$typeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
