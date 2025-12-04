<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;

use App\Helpers\CodeGenerator;
use PDO;


class ProductTypeModel
{
    // List semua jenis produk dalam section
    public static function listBySection($sectionId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM product_types WHERE section_id = ? ORDER BY name ASC");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil detail jenis produk
    public static function find($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM product_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah jenis produk
    public static function create($sectionId, $data, $files)
    {
        $pdo = Database::connect();
        $section = ConsumableModel::getSectionById($sectionId);

        if (!$section || !$data) {
            echo "Error: section atau data tidak valid.";
            return;
        }

        $productCode = \App\Helpers\CodeGenerator::generateProductCode($section['name'], $data['name']);

        // ✅ Upload gambar dan file
        $imagePath = !empty($files['image']['name']) ? self::uploadFile($files['image']) : null;
        $filePath  = !empty($files['file_path']['name']) ? self::uploadFile($files['file_path']) : null;

        $stmt = $pdo->prepare("
        INSERT INTO product_types (section_id, product_code, name, description, price, image_path, file_path)
        VALUES (:section_id, :product_code, :name, :description, :price, :image_path, :file_path)
    ");
        $stmt->execute([
            ':section_id'   => $sectionId,
            ':product_code' => $productCode,
            ':name'         => $data['name'],
            ':description'  => $data['description'] ?? null,
            ':price' => is_numeric($data['price']) ? $data['price'] : null,
            ':image_path'   => $imagePath,
            ':file_path'    => $filePath,
        ]);
    }

    // Update jenis produk
    public static function update($id, $data, $files)
    {
        $pdo = Database::connect();

        // Ambil data lama dulu
        $old = self::find($id);

        $imagePath = !empty($files['image']['name']) ? self::uploadFile($files['image']) : $data['old_image'];
        $filePath  = !empty($files['file_path']['name']) ? self::uploadFile($files['file_path']) : $data['old_file'];

        $stmt = $pdo->prepare("
        UPDATE product_types 
        SET section_id=?, product_code=?, name=?, price=?, description=?, image_path=?, file_path=? 
        WHERE id=?
    ");
        $stmt->execute([
            $old['section_id'],              // pakai section_id lama
            $old['product_code'],            // pakai product_code lama
            $data['name'],
            !empty($data['price']) ? $data['price'] : null,
            $data['description'],
            $imagePath,
            $filePath,
            $id
        ]);
    }

    // Hapus jenis produk
    public static function delete($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM product_types WHERE id=?");
        $stmt->execute([$id]);
    }

    // List item dalam jenis produk
    public static function listItems($productTypeId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM product_items WHERE product_type_id = ? ORDER BY name ASC");
        $stmt->execute([$productTypeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function uploadFile($file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;
        $targetDir = __DIR__ . '/../../public/uploads/consum-product-type/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $target = $targetDir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $target);
        return '/uploads/consum-product-type/' . basename($file['name']);
    }
}
