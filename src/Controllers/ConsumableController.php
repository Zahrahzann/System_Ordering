<?php

namespace App\Controllers;

use App\Models\SectionModel;

class ConsumableController
{
    // List Section (semua role bisa lihat)
    public static function listSection()
    {
        $sections = SectionModel::getAll();

        // Ambil role dari session
        $currentRole = $_SESSION['user_data']['role'] ?? 'customer';

        // Tambahan: handle edit mode (khusus admin)
        $isEditMode = false;
        $editData = null;
        if ($currentRole === 'admin' && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $isEditMode = true;
            $editData = SectionModel::find($_GET['edit']);
        }

        // Kirim data ke view shared
        require_once __DIR__ . '/../../views/shared/sections.php';
    }

    // Tambah Section (admin only)
    public static function addSection()
    {
        $currentRole = $_SESSION['user_data']['role'] ?? null;
        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa tambah section.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;

            SectionModel::create($name, $description);
            header('Location: /system_ordering/public/admin/consumable/sections');
            exit;
        }

        // tampilkan form create (admin only)
        require_once __DIR__ . '/../../views/shared/section_form.php';
    }

    // Edit Section (admin only)
    public static function editSection($id)
    {
        $currentRole = $_SESSION['user_data']['role'] ?? null;
        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa edit section.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;

            SectionModel::update($id, $name, $description);
            header('Location: /system_ordering/public/admin/consumable/sections');
            exit;
        }

        $section = SectionModel::find($id); // satu record
        require_once __DIR__ . '/../../views/shared/section_form.php';
    }

    // Hapus Section (admin only)
    public static function deleteSection($id)
    {
        $currentRole = $_SESSION['user_data']['role'] ?? null;
        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa hapus section.";
            exit;
        }

        SectionModel::delete($id);
        header('Location: /system_ordering/public/admin/consumable/sections');
        exit;
    }

    // List produk consumable (semua role bisa lihat)
    // public static function listProducts()
    // {
    //     $products = ProductModel::getAllConsumables();
    //     $currentRole = $_SESSION['user_data']['role'] ?? 'customer';

    //     require_once __DIR__ . '/../../views/shared/katalog_produk.php';
    // }

    // // Tambah produk (admin only)
    // public static function addProduct()
    // {
    //     $currentRole = $_SESSION['user_data']['role'] ?? null;
    //     if ($currentRole !== 'admin') {
    //         http_response_code(403);
    //         echo "Forbidden: hanya admin yang bisa tambah produk.";
    //         exit;
    //     }

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         ProductModel::createConsumable($_POST);
    //         header('Location: /system_ordering/public/admin/consumable/katalog_produk');
    //         exit;
    //     }

    //     $sections = SectionModel::getAll();
    //     require_once __DIR__ . '/../../views/shared/product_form.php';
    // }

    // // Edit produk (admin only)
    // public static function editProduct($id)
    // {
    //     $currentRole = $_SESSION['user_data']['role'] ?? null;
    //     if ($currentRole !== 'admin') {
    //         http_response_code(403);
    //         echo "Forbidden: hanya admin yang bisa edit produk.";
    //         exit;
    //     }

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         ProductModel::updateConsumable($id, $_POST);
    //         header('Location: /system_ordering/public/admin/consumable/katalog_produk');
    //         exit;
    //     }

    //     $product = ProductModel::find($id);
    //     $sections = SectionModel::getAll();
    //     require_once __DIR__ . '/../../views/shared/product_form.php';
    // }

    // // Hapus produk (admin only)
    // public static function deleteProduct($id)
    // {
    //     $currentRole = $_SESSION['user_data']['role'] ?? null;
    //     if ($currentRole !== 'admin') {
    //         http_response_code(403);
    //         echo "Forbidden: hanya admin yang bisa hapus produk.";
    //         exit;
    //     }

    //     ProductModel::delete($id);
    //     header('Location: /system_ordering/public/admin/consumable/katalog_produk');
    //     exit;
    // }
}
