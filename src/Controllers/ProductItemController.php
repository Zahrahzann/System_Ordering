<?php
namespace App\Controllers;

use App\Models\ProductItemModel;

class ProductItemController
{
    // List printilan per jenis produk
    public static function listByProductType($typeId)
    {
        $items = ProductItemModel::listByProductType($typeId);
        require_once __DIR__ . '/../../views/shared/product_items.php';
    }

    // Tambah printilan (admin only)
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
            header("Location: /admin/consumable/product-items?type={$typeId}");
            exit;
        } else {
            require_once __DIR__ . '/../../views/shared/product_item_form.php';
        }
    }

    // Edit printilan
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
            header("Location: /admin/consumable/product-items");
            exit;
        } else {
            $item = ProductItemModel::find($id);
            require_once __DIR__ . '/../../views/shared/product_item_form.php';
        }
    }

    // Hapus printilan
    public static function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        ProductItemModel::delete($id);
        header("Location: /admin/consumable/product-items");
        exit;
    }
}
