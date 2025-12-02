<?php

namespace App\Controllers;

use App\Models\ProductTypeModel;
use App\Models\ConsumableModel;

class ProductTypeController
{
    // List jenis produk per section
    public static function listBySection($sectionId)
    {
        $productTypes = ProductTypeModel::listBySection($sectionId);
        $section = ConsumableModel::getSectionById($sectionId);
        require_once __DIR__ . '/../../views/shared/product-types.php';
    }

    // Detail jenis produk
    public static function detail($id)
    {
        $productType = ProductTypeModel::find($id);
        $items = ProductTypeModel::listItems($id);
        require_once __DIR__ . '/../../views/shared/product-type_detail.php';
    }

    // Tambah jenis produk (admin only)
    public static function add($sectionId)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProductTypeModel::create($sectionId, $_POST, $_FILES);
            header("Location: /system_ordering/public/admin/consumable/product-types?section={$sectionId}");
            exit;
        } else {
            // langsung pakai view product-types.php
            $productTypes = ProductTypeModel::listBySection($sectionId);
            $section = ConsumableModel::getSectionById($sectionId);
            $editData = null;
            require_once __DIR__ . '/../../views/shared/product-types.php';
        }
    }

    // Edit jenis produk
    public static function edit($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProductTypeModel::update($id, $_POST, $_FILES);

            // ambil section_id dari data POST supaya redirect ke section yang benar
            $sectionId = $_POST['section_id'] ?? null;
            header("Location: /system_ordering/public/admin/consumable/product-types?section={$sectionId}");
            exit;
        } else {
            // ambil data produk yang mau diedit
            $productType = ProductTypeModel::find($id);

            // ambil section dan semua productTypes untuk ditampilkan di view
            $section = ConsumableModel::getSectionById($productType['section_id']);
            $productTypes = ProductTypeModel::listBySection($productType['section_id']);

            // set editData supaya form edit muncul di view
            $editData = $productType;

            require_once __DIR__ . '/../../views/shared/product-types.php';
        }
    }

    // Hapus jenis produk
    public static function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SESSION['user_data']['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin.";
            exit;
        }

        // Ambil section_id dari jenis produk sebelum hapus
        $productType = ProductTypeModel::find($id);
        $sectionId = $productType['section_id'] ?? null;

        if (!$sectionId) {
            echo "Error: section ID tidak valid.";
            exit;
        }

        ProductTypeModel::delete($id);
        header("Location: /system_ordering/public/admin/consumable/product-types?section={$sectionId}");
        exit;
    }
}
