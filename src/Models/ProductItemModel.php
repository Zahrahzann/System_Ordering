<?php

namespace App\Models;

use PDO;
use ManufactureEngineering\SystemOrdering\Config\Database;
use App\Helpers\CodeGenerator;

class ProductItemModel
{
    private static function db(): PDO
    {
        return Database::connect();
    }

    // List item per jenis produk
    public static function listByProductType($typeId): array
    {
        $st = self::db()->prepare("SELECT * FROM product_items WHERE product_type_id = ? ORDER BY name ASC");
        $st->execute([$typeId]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Ambil detail item
    public static function find($id): ?array
    {
        $st = self::db()->prepare("SELECT * FROM product_items WHERE id = ?");
        $st->execute([$id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Tambah item
    public static function create($typeId, $data, $files): void
    {
        $productType = ProductTypeModel::find($typeId);
        $itemCode    = CodeGenerator::generateItemCode($data['name'], $productType['name']);
        $sectionId   = $productType['section_id']; 

        $imagePath = self::uploadFile($files['image'] ?? null);
        $filePath  = self::uploadFile($files['file_path'] ?? null);

        $st = self::db()->prepare("
        INSERT INTO product_items (product_type_id, item_code, name, price, description, image_path, file_path, stock, section_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
        $st->execute([
            $typeId,
            $itemCode,
            $data['name'],
            is_numeric($data['price']) ? $data['price'] : null,
            $data['description'],
            $imagePath,
            $filePath,
            is_numeric($data['stock']) ? $data['stock'] : 0,
            $sectionId
        ]);
    }


    // Update item
    public static function update($id, $data, $files): void
    {
        $oldItem = self::find($id);
        if (!$oldItem) {
            throw new \Exception("Item not found");
        }

        $imagePath = (!empty($files['image']['name']))
            ? self::uploadFile($files['image'])
            : $oldItem['image_path'];

        $filePath = (!empty($files['file_path']['name']))
            ? self::uploadFile($files['file_path'])
            : $oldItem['file_path'];

        $productType = ProductTypeModel::find($oldItem['product_type_id']);
        $sectionId   = $productType['section_id'];

        $st = self::db()->prepare("
        UPDATE product_items 
        SET product_type_id=?, item_code=?, name=?, price=?, description=?, image_path=?, file_path=?, stock=?, section_id=? 
        WHERE id=?
    ");
        $st->execute([
            $oldItem['product_type_id'], 
            $oldItem['item_code'],
            $data['name'],
            is_numeric($data['price']) ? $data['price'] : null,
            $data['description'],
            $imagePath,
            $filePath,
            is_numeric($data['stock']) ? $data['stock'] : 0,
            $sectionId,
            $id
        ]);
    }

    // Hapus item
    public static function delete($id): void
    {
        $st = self::db()->prepare("DELETE FROM product_items WHERE id=?");
        $st->execute([$id]);
    }

    private static function uploadFile($file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

        $targetDir = '/uploads/consum-katalog-item/';
        $target    = $targetDir . basename($file['name']);


        $destination = __DIR__ . '/../../public' . $target;
        if (!is_dir(__DIR__ . '/../../public' . $targetDir)) {
            mkdir(__DIR__ . '/../../public' . $targetDir, 0777, true);
        }
        move_uploaded_file($file['tmp_name'], $destination);

        return $target;
    }
}
