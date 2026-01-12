<?php

namespace App\Controllers;

use App\Models\ProductItemModel;
use App\Models\ProductTypeModel;
use App\Models\ConsumOrderModel;

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
            // Pastikan maker_price ikut dikirim ke model
            $data = $_POST;
            $data['maker_price'] = isset($_POST['maker_price']) ? (float)$_POST['maker_price'] : null;

            ProductItemModel::create($typeId, $data, $_FILES);

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
            // Update produk, termasuk maker_price
            $data = $_POST;
            $data['maker_price'] = isset($_POST['maker_price']) ? (float)$_POST['maker_price'] : null;

            ProductItemModel::update($id, $data, $_FILES);

            // Setelah stok diupdate → cek order pending
            ConsumOrderModel::checkAndUpdatePendingOrders($id);

            $item   = ProductItemModel::find($id);
            $typeId = $item['product_type_id'] ?? null;

            header("Location: /system_ordering/public/admin/consumable/product-items?type={$typeId}&status=stock_updated");
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
