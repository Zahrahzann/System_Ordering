<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;

class SectionModel
{
    public static function getAll()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM sections ORDER BY id ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM sections WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create($name, $description = null, $files = null)
    {
        $pdo = Database::connect();

        // Cek apakah nama sudah ada
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE name = :name");
        $stmt->execute([':name' => $name]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['errors'][] = "Nama section '$name' sudah ada.";
            return false;
        }

        // Upload gambar jika ada
        $imagePath = (!empty($files['image']['name'])) ? self::uploadFile($files['image']) : null;

        $stmt = $pdo->prepare("INSERT INTO sections (name, description, image) VALUES (:name, :description, :image)");
        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image' => $imagePath
        ]);
    }

    public static function update($id, $name, $description = null, $files = null, $oldImage = null)
    {
        $pdo = Database::connect();

        // Cek apakah nama dipakai section lain
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE name = :name AND id != :id");
        $stmt->execute([':name' => $name, ':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['errors'][] = "Nama section '$name' sudah dipakai section lain.";
            return false;
        }

        // Upload gambar baru kalau ada, kalau tidak pakai gambar lama
        $imagePath = (!empty($files['image']['name'])) ? self::uploadFile($files['image']) : $oldImage;

        $stmt = $pdo->prepare("UPDATE sections SET name = :name, description = :description, image = :image WHERE id = :id");
        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image' => $imagePath,
            ':id' => $id
        ]);
    }

    public static function delete($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM sections WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    private static function uploadFile($file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['errors'][] = "Format gambar tidak didukung.";
            return null;
        }

        $targetDir = __DIR__ . '/../../public/uploads/consum-section/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $originalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
        $filename     = time() . '_' . $originalName;
        $target       = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            // simpan relatif path
            return 'uploads/consum-section/' . $filename;
        }

        $_SESSION['errors'][] = "Gagal upload file.";
        return null;
    }
}
