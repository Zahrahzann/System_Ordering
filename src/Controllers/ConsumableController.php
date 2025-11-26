<?php

namespace App\Controllers;

use App\Models\SectionModel;

class ConsumableController
{
    // List Section
    public static function listSection()
    {
        $sections = SectionModel::getAll();

        // Tambahan: handle create/edit mode
        $isEditMode = false;
        $editData = null;

        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $isEditMode = true;
            $editData = SectionModel::find($_GET['edit']);
        }

        require_once __DIR__ . '/../../views/admin/consumable/sections.php';
    }

    // Tambah Section
    public static function addSection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;

            SectionModel::create($name, $description);
            header('Location: /admin/consumable/sections');
            exit;
        }

        // tampilkan form create
        require_once __DIR__ . '/../../views/admin/consumable/section_form.php';
    }

    // Edit Section
    public static function editSection($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;

            SectionModel::update($id, $name, $description);
            header('Location: /admin/consumable/sections');
            exit;
        }

        $section = SectionModel::find($id); // satu record, bukan array
        require_once __DIR__ . '/../../views/admin/consumable/section_form.php';
    }

    // Hapus Section
    public static function deleteSection($id)
    {
        SectionModel::delete($id);
        header('Location: /admin/consumable/sections');
        exit;
    }




    // // List produk consumable
    // public static function listProducts()
    // {
    //     $products = ProductModel::getAllConsumables();
    //     require_once __DIR__ . '/../../views/admin/consumable/katalog_produk.php';
    // }

    // // Tambah produk
    // public static function addProduct()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         ProductModel::createConsumable($_POST);
    //         header('Location: /admin/consumable/katalog_produk');
    //         exit;
    //     }
    //     $sections = SectionModel::getAll();
    //     require_once __DIR__ . '/../../views/admin/consumable/product_form.php';
    // }

    // // Edit produk
    // public static function editProduct($id)
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         ProductModel::updateConsumable($id, $_POST);
    //         header('Location: /admin/consumable/katalog_produk');
    //         exit;
    //     }
    //     $product = ProductModel::find($id);
    //     $sections = SectionModel::getAll();
    //     require_once __DIR__ . '/../../views/admin/consumable/product_form.php';
    // }

    // // Hapus produk
    // public static function deleteProduct($id)
    // {
    //     ProductModel::delete($id);
    //     header('Location: /admin/consumable/katalog_produk');
    //     exit;
    // }
}
