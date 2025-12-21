<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class MaterialTypeModel
{
    public static function getAll()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM material_types ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM material_types WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Create type baru, return ID untuk dipakai insert dimension */
    public static function create($data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            INSERT INTO material_types (material_number, name)
            VALUES (:material_number, :name)
        ");
        $stmt->bindParam(':material_number', $data['material_number']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->execute();
        return $pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            UPDATE material_types
            SET material_number = :material_number, name = :name
            WHERE id = :id
        ");
        $stmt->bindParam(':material_number', $data['material_number']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM material_types WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
