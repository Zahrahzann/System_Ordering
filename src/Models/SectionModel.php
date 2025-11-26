<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;

class SectionModel
{
    public static function getAll()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM sections ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM sections WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function create($name, $description = null)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO sections (name, description) VALUES (:name, :description)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    }

    public static function update($id, $name, $description = null)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE sections SET name = :name, description = :description WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM sections WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
