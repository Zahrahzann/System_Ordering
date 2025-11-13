<?php

namespace App\Controllers;

use ManufactureEngineering\SystemOrdering\Config\Database;
use Respect\Validation\Validator as v;
use App\Models\CartModel;
use App\Middleware\SessionMiddleware;

class WorkOrderController
{
    /**
     * CREATE (Tambah Item Baru)
     */
    public static function processWorkOrderForm()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $errors = self::validateWorkOrderData($_POST, $_FILES);
        if (!empty($errors)) {
            self::handleErrors($errors);
            return;
        }

        $filePathsJson = self::handleMultipleFileUploads($_FILES['file_path']);
        if ($filePathsJson === false) {
            self::handleErrors(['Gagal mengupload file. Pastikan formatnya benar (JPG, PNG, PDF) dan ukuran tidak lebih dari 5MB.']);
            return;
        }

        $pdo = Database::connect();
        $sql = "INSERT INTO items (customer_id, order_id, item_type, item_name, category, quantity, material, material_type, file_path, needed_date, note, is_emergency, emergency_type)
        VALUES (:customer_id, NULL, 'work_order', :item_name, :category, :quantity, :material, :material_type, :file_path, :needed_date, :note, :is_emergency, :emergency_type)";
        $stmt = $pdo->prepare($sql);

        $isEmergency = isset($_POST['is_emergency']) ? 1 : 0;
        $emergencyType = $isEmergency ? ($_POST['emergency_type'] ?? null) : null;

        $stmt->execute([
            ':customer_id'   => $_SESSION['user_data']['id'],
            ':item_name'     => $_POST['item_name'],
            ':category'      => $_POST['category'],
            ':quantity'      => (int)$_POST['quantity'],
            ':material'      => $_POST['material'],
            ':material_type' => $_POST['material_type'],
            ':file_path'     => $filePathsJson,
            ':needed_date'   => date('Y-m-d H:i:s', strtotime($_POST['needed_date'])),
            ':note'          => $_POST['note'],
            ':is_emergency'  => $isEmergency,
            ':emergency_type' => $emergencyType
        ]);

        $action = $_POST['action_type'] ?? 'cart';
        if ($action === 'cart') {
            $_SESSION['success'] = 'Item berhasil ditambahkan ke keranjang!';
            header('Location: /system_ordering/public/customer/cart');
            exit;
        } elseif ($action === 'order') {
            $_SESSION['success'] = 'Item berhasil diorder!';
            header('Location: /system_ordering/public/customer/cart');
            exit;
        }
    }

    /**
     * Menampilkan form untuk meng-edit item yang ada
     */
    public static function editItem($itemId)
    {
        SessionMiddleware::requireCustomerLogin();
        $itemId = (int) $itemId;
        $customerId = $_SESSION['user_data']['id'];
        $item = CartModel::getItemById($itemId, $customerId);

        if (!$item) {
            header('Location: /system_ordering/public/customer/cart');
            exit;
        }

        require_once __DIR__ . '/../../views/customer/work_order/form.php';
    }

    /**
     * Memproses data dari form UPDATE
     */
    public static function updateItem($itemId)
    {
        SessionMiddleware::requireCustomerLogin();
        $itemId = (int) $itemId;
        $customerId = $_SESSION['user_data']['id'];

        $errors = self::validateWorkOrderDataForUpdate($_POST);
        if (!empty($errors)) {
            self::handleErrors($errors, '/system_ordering/public/customer/cart/edit/' . $itemId);
            return;
        }

        $oldItem = CartModel::getItemById($itemId, $customerId);
        if (!$oldItem) {
            header('Location: /system_ordering/public/customer/cart');
            exit;
        }

        $filePathsJson = $oldItem['file_path'];
        if (!empty($_FILES['file_path']['name'][0])) {
            $newFilePathsJson = self::handleMultipleFileUploads($_FILES['file_path']);
            if ($newFilePathsJson) {
                $filePathsJson = $newFilePathsJson;
            }
        }

        $isEmergency = isset($_POST['is_emergency']) ? 1 : 0;
        $emergencyType = $isEmergency ? ($_POST['emergency_type'] ?? null) : null;

        $data = [
            'item_name'      => $_POST['item_name'],
            'category'       => $_POST['category'],
            'quantity'       => (int)$_POST['quantity'],
            'material'       => $_POST['material'],
            'material_type'  => $_POST['material_type'],
            'file_path'      => $filePathsJson,
            'needed_date'    => date('Y-m-d H:i:s', strtotime($_POST['needed_date'])),
            'note'           => $_POST['note'],
            'is_emergency'   => $isEmergency,
            'emergency_type' => $emergencyType
        ];

        CartModel::updateItem($itemId, $data, $customerId);

        header('Location: /system_ordering/public/customer/cart?status=item_updated');
        exit;
    }

    // --- Helper Functions ---
    private static function validateWorkOrderData(array $post, array $files)
    {
        $errors = [];
        if (!v::stringType()->length(3, 255)->validate($post['item_name'] ?? '')) $errors[] = "Nama Part harus diisi (min 3 karakter).";
        if (empty($post['category'])) $errors[] = "Kategori wajib dipilih.";
        if (empty($post['material'])) $errors[] = "Material wajib dipilih.";
        if (empty($post['material_type'])) $errors[] = "Jenis Material wajib diisi.";
        if (!v::intVal()->positive()->validate($post['quantity'] ?? '')) $errors[] = "Quantity harus berupa angka.";
        if (empty($post['needed_date'])) $errors[] = "Tanggal dibutuhkan wajib diisi.";
        if (isset($post['is_emergency']) && empty($post['emergency_type'])) $errors[] = "Jika Emergency dicentang, Jenis Emergency wajib dipilih.";

        if (empty($files['file_path']['name'][0])) {
            if (!isset($post['item_id'])) {
                $errors[] = "Minimal satu file gambar drawing wajib diupload.";
            }
        }
        return $errors;
    }

    private static function validateWorkOrderDataForUpdate(array $post)
    {
        $errors = [];
        if (!v::stringType()->length(3, 255)->validate($post['item_name'] ?? '')) $errors[] = "Nama Part harus diisi (min 3 karakter).";
        if (empty($post['category'])) $errors[] = "Kategori wajib dipilih.";
        if (empty($post['material'])) $errors[] = "Material wajib dipilih.";
        if (empty($post['material_type'])) $errors[] = "Material wajib diisi.";
        if (!v::intVal()->positive()->validate($post['quantity'] ?? '')) $errors[] = "Quantity harus berupa angka.";
        if (empty($post['needed_date'])) $errors[] = "Tanggal dibutuhkan wajib diisi.";
        if (isset($post['is_emergency']) && empty($post['emergency_type'])) $errors[] = "Jika Emergency dicentang, Jenis Emergency wajib dipilih.";
        return $errors;
    }
    private static function handleMultipleFileUploads(array $files)
    {
        if (empty($files['name'][0])) return "[]";
        $uploadedPaths = [];
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            $uploadDir = __DIR__ . '/../../public/uploads/drawings/';

            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $extension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!in_array($extension, $allowed) || $files['size'][$i] > 2097152) return false;
            $uniqueFilename = uniqid('drawing_', true) . '.' . $extension;
            $destination = $uploadDir . $uniqueFilename;
            if (move_uploaded_file($files['tmp_name'][$i], $destination)) {

                $uploadedPaths[] = '/system_ordering/public/uploads/drawings/' . $uniqueFilename;
            } else {
                return false;
            }
        }
        return json_encode($uploadedPaths);
    }

    private static function handleErrors(array $errors, $redirectPath = null)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: ' . ($redirectPath ?? $_SERVER['HTTP_REFERER']));
        exit;
    }
}
