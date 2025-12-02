<?php

namespace App\Models;

use PDO;

class ProductItemModel
{
    private static function db()
    {
        return $GLOBALS['db'];
    }

    // List printilan per jenis produk
    public static function listByProductType($typeId)
    {
        $st = self::db()->prepare("SELECT * FROM product_items WHERE product_type_id = ? ORDER BY name ASC");
        $st->execute([$typeId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil detail printilan
    public static function find($id)
    {
        $st = self::db()->prepare("SELECT * FROM product_items WHERE id = ?");
        $st->execute([$id]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah printilan
    public static function create($typeId, $data, $files)
    {
        $imagePath = self::uploadFile($files['image'] ?? null);
        $filePath  = self::uploadFile($files['file_path'] ?? null);

        $st = self::db()->prepare("
        INSERT INTO product_items (product_type_id, item_code, name, price, description, image_path, file_path)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
        $st->execute([
            $typeId,
            $data['item_code'],
            $data['name'],
            is_numeric($data['price']) ? $data['price'] : null,
            $data['description'],
            $imagePath,
            $filePath
        ]);
    }

    // Update printilan
    public static function update($id, $data, $files)
    {
        $imagePath = !empty($files['image']['name']) ? self::uploadFile($files['image']) : $data['old_image'];
        $filePath  = !empty($files['file_path']['name']) ? self::uploadFile($files['file_path']) : $data['old_file'];

        $st = self::db()->prepare("
        UPDATE product_items 
        SET product_type_id=?, item_code=?, name=?, price=?, description=?, image_path=?, file_path=? 
        WHERE id=?
    ");
        $st->execute([
            $data['product_type_id'],
            $data['item_code'],
            $data['name'],
            is_numeric($data['price']) ? $data['price'] : null,
            $data['description'],
            $imagePath,
            $filePath,
            $id
        ]);
    }

    // Hapus printilan
    public static function delete($id)
    {
        $st = self::db()->prepare("DELETE FROM product_items WHERE id=?");
        $st->execute([$id]);
    }

    private static function uploadFile($file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;
        $target = '/uploads/consum-product-item/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../../public' . $target);
        return $target;
    }
}
