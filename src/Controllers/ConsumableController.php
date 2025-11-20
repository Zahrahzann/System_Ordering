<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class ConsumableController
{
    // List kategori
    public static function listCategories()
    {
        $categories = CategoryModel::getAll();
        require_once __DIR__ . '/../../views/admin/consumable/katalog_kategori.php';
    }

    // Tambah kategori
    public static function addCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;

            CategoryModel::create($name, $description);
            header('Location: /admin/consumable/katalog_kategori');
            exit;
        }

        require_once __DIR__ . '/../../views/admin/consumable/kategori_form.php';
    }

    // Edit kategori
    public static function editCategory($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = $_POST['description'] ?? null;

            CategoryModel::update($id, $description);
            header('Location: /admin/consumable/katalog_kategori');
            exit;
        }

        $category = CategoryModel::find($id);
        require_once __DIR__ . '/../../views/admin/consumable/kategori_form.php';
    }

    // Hapus kategori
    public static function deleteCategory($id)
    {
        CategoryModel::delete($id);
        header('Location: /admin/consumable/katalog_kategori');
        exit;
    }

    // List produk consumable
    public static function listProducts()
    {
        $products = ProductModel::getAllConsumables();
        require_once __DIR__ . '/../../views/admin/consumable/katalog_produk.php';
    }

    // Tambah produk
    public static function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProductModel::createConsumable($_POST);
            header('Location: /admin/consumable/katalog_produk');
            exit;
        }
        $categories = CategoryModel::getAll();
        require_once __DIR__ . '/../../views/admin/consumable/product_form.php';
    }

    // Edit produk
    public static function editProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProductModel::updateConsumable($id, $_POST);
            header('Location: /admin/consumable/katalog_produk');
            exit;
        }
        $product = ProductModel::find($id);
        $categories = CategoryModel::getAll();
        require_once __DIR__ . '/../../views/admin/consumable/product_form.php';
    }

    // Hapus produk
    public static function deleteProduct($id)
    {
        ProductModel::delete($id);
        header('Location: /admin/consumable/katalog_produk');
        exit;
    }
}
