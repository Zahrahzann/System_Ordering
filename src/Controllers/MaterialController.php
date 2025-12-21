<?php

namespace App\Controllers;

use App\Models\MaterialTypeModel;
use App\Models\MaterialDimensionModel;
use App\Models\MaterialStockLogModel;
use App\Middleware\SessionMiddleware;

class MaterialController
{
    /** Menampilkan daftar material (Admin only) */
    public static function index()
    {
        SessionMiddleware::requireLogin();

        $title      = "Kelola Material";
        $types      = MaterialTypeModel::getAll();
        $dimensions = MaterialDimensionModel::getAllGrouped();
        $basePath   = "/system_ordering/public";

        require_once __DIR__ . '/../../views/admin/work_order/materials.php';
    }

    /** Simpan jenis material baru (TYPE) */
    public static function storeType()
    {
        SessionMiddleware::requireAdminLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi sederhana
            if (empty($_POST['material_number']) || empty($_POST['name'])) {
                $_SESSION['flash_notification'] = [
                    'type'    => 'danger',
                    'message' => 'Nomor dan nama material wajib diisi.'
                ];
                header('Location: /system_ordering/public/admin/materials');
                exit;
            }

            MaterialTypeModel::create([
                'material_number' => $_POST['material_number'],
                'name'            => $_POST['name']
            ]);

            $_SESSION['flash_notification'] = [
                'type'    => 'success',
                'message' => 'Jenis material berhasil ditambahkan.'
            ];
            header('Location: /system_ordering/public/admin/materials');
            exit;
        }
    }

    /** Simpan dimensi material baru (DIMENSION) */
    public static function storeDimension()
    {
        SessionMiddleware::requireAdminLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['material_type_id']) || empty($_POST['dimension']) || $_POST['stock'] === '') {
                $_SESSION['flash_notification'] = [
                    'type'    => 'danger',
                    'message' => 'Dimensi dan stok wajib diisi.'
                ];
                header('Location: /system_ordering/public/admin/materials');
                exit;
            }

            MaterialDimensionModel::create([
                'material_type_id' => (int)$_POST['material_type_id'],
                'dimension'        => $_POST['dimension'],
                'stock'            => (float)$_POST['stock']
            ]);

            $_SESSION['flash_notification'] = [
                'type'    => 'success',
                'message' => 'Dimensi material berhasil ditambahkan.'
            ];
            header('Location: /system_ordering/public/admin/materials');
            exit;
        }
    }

    /** Update dimension (Admin only) */
    public static function updateDimension($id)
    {
        SessionMiddleware::requireAdminLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = MaterialDimensionModel::getById((int)$id);
            $newStock = (float)($_POST['stock'] ?? 0);

            // Update stok
            MaterialDimensionModel::update((int)$id, [
                'dimension' => $_POST['dimension'] ?? '',
                'stock'     => $newStock
            ]);

            // Catat log stok
            if ($newStock != $old['stock']) {
                $changeType = $newStock > $old['stock'] ? 'IN' : 'OUT';
                $quantity   = abs($newStock - $old['stock']);

                MaterialStockLogModel::create([
                    'material_dimension_id' => (int)$id,
                    'change_type'           => $changeType,
                    'quantity'              => $quantity
                ]);
            }

            $_SESSION['flash_notification'] = [
                'type'    => 'success',
                'message' => 'Dimensi material berhasil diperbarui.'
            ];
            header('Location: /system_ordering/public/admin/materials');
            exit;
        }
    }

    /** Update type (Admin only) */
    public static function updateType($id)
    {
        SessionMiddleware::requireAdminLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            MaterialTypeModel::update((int)$id, [
                'material_number' => $_POST['material_number'] ?? '',
                'name'            => $_POST['name'] ?? ''
            ]);
            $_SESSION['flash_notification'] = [
                'type'    => 'success',
                'message' => 'Jenis material berhasil diperbarui.'
            ];
            header('Location: /system_ordering/public/admin/materials');
            exit;
        }
    }

    /** Hapus dimension */
    public static function destroyDimension($id)
    {
        SessionMiddleware::requireAdminLogin();
        MaterialDimensionModel::delete((int)$id);
        $_SESSION['flash_notification'] = [
            'type'    => 'success',
            'message' => 'Dimensi material berhasil dihapus.'
        ];
        header('Location: /system_ordering/public/admin/materials');
        exit;
    }

    /** Hapus type (beserta semua dimensinya) */
    public static function destroyType($id)
    {
        SessionMiddleware::requireAdminLogin();
        MaterialDimensionModel::deleteByType((int)$id);
        MaterialTypeModel::delete((int)$id);

        $_SESSION['flash_notification'] = [
            'type'    => 'success',
            'message' => 'Jenis material berhasil dihapus.'
        ];
        header('Location: /system_ordering/public/admin/materials');
        exit;
    }

    /** Customer: ambil data dimension untuk AJAX stock */
    public static function showDimension($id)
    {
        SessionMiddleware::requireCustomerLogin();
        header('Content-Type: application/json');
        echo json_encode(MaterialDimensionModel::getById((int)$id));
        exit;
    }

    /** Customer: ambil semua jenis material untuk dropdown */
    public static function getTypes()
    {
        SessionMiddleware::requireCustomerLogin();
        header('Content-Type: application/json');
        echo json_encode(MaterialTypeModel::getAll());
        exit;
    }

    /** Customer: ambil semua dimensi berdasarkan type_id */
    public static function getDimensionsByType($typeId)
    {
        SessionMiddleware::requireCustomerLogin();
        header('Content-Type: application/json');
        echo json_encode(MaterialDimensionModel::getByType((int)$typeId));
        exit;
    }
}
