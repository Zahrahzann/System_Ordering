<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ConsumableController
{
    public static function showCatalog()
    {
        $category = $_GET['category'] ?? null;

        if (!$category) {
            // tampilkan daftar kategori slipper
            $categories = ['Presub', 'K-Line 3', 'SPS K4', 'K-Line 5', 'Delivery'];
            require_once __DIR__ . '/../../views/customer/consumable/catalog_category.php';
            return;
        }

        // ambil produk consumable berdasarkan kategori
        $products = ProductModel::getConsumableByCategory($category);

        require_once __DIR__ . '/../../views/customer/consumable/catalog_list.php';
    }
}
