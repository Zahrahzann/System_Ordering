<?php

namespace App\Controllers;

use App\Models\ProductItemModel;
use App\Models\ProductTypeModel;

class ProductItemController
{
    // List printilan per jenis produk
    public static function listByProductType($typeId)
    {
        $items       = ProductItemModel::listByProductType($typeId);
        $productType = ProductTypeModel::find($typeId);

        require_once __DIR__ . '/../../views/shared/product-items.php';
    }

    // Tambah printilan (admin only, via modal)
    public static function add($typeId)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProductItemModel::create($typeId, $_POST, $_FILES);
            header("Location: /system_ordering/public/admin/consumable/product-items?type={$typeId}");
            exit;
        } else {
            echo "Form tambah printilan hanya tersedia via modal.";
            exit;
        }
    }

    // Edit printilan (admin only, via modal)
    public static function edit($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProductItemModel::update($id, $_POST, $_FILES);

            $item   = ProductItemModel::find($id);
            $typeId = $item['product_type_id'] ?? null;

            header("Location: /system_ordering/public/admin/consumable/product-items?type={$typeId}");
            exit;
        }
    }

    // Hapus printilan (admin only)
    public static function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        $item   = ProductItemModel::find($id);
        $typeId = $item['product_type_id'] ?? null;

        ProductItemModel::delete($id);

        header("Location: /system_ordering/public/admin/consumable/product-items?type={$typeId}");
        exit;
    }
}
