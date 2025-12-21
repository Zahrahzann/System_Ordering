<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class MaterialStockLogModel
{
    /** Simpan log stok baru */
    public static function create($data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            INSERT INTO material_stock_logs (material_dimension_id, change_type, quantity)
            VALUES (:material_dimension_id, :change_type, :quantity)
        ");
        $stmt->bindParam(':material_dimension_id', $data['material_dimension_id'], PDO::PARAM_INT);
        $stmt->bindParam(':change_type', $data['change_type']);
        $stmt->bindParam(':quantity', $data['quantity']);
        return $stmt->execute();
    }

    /** Ambil semua log berdasarkan dimension */
    public static function getByDimension($dimensionId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT id, material_dimension_id, change_type, quantity, created_at
            FROM material_stock_logs
            WHERE material_dimension_id = :dimensionId
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':dimensionId', $dimensionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
