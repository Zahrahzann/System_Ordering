<?php

namespace App\Controllers;

use App\Models\SectionModel;
use App\Models\ProductTypeModel;

class ConsumableController
{
    public static function listSection()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $sections = SectionModel::getAll();
        $currentRole = $_SESSION['user_data']['role'] ?? 'customer';

        foreach ($sections as $i => $sec) {
            $sections[$i]['item_count'] = ProductTypeModel::countBySection($sec['id']);
        }

        $isEditMode = false;
        $editData = null;

        if ($currentRole === 'admin' && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $editData = SectionModel::find($_GET['edit']);
            $isEditMode = $editData !== null;
        }

        require_once __DIR__ . '/../../views/shared/sections.php';
    }

    public static function addSection()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentRole = $_SESSION['user_data']['role'] ?? null;

        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa tambah section.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name'] ?? '');
            $description = $_POST['description'] ?? null;

            if ($name === '') {
                $_SESSION['errors'][] = "Nama section tidak boleh kosong.";
            } else {
                // kirim $_FILES ke model untuk handle upload gambar
                $ok = SectionModel::create($name, $description, $_FILES);
                if ($ok) {
                    header('Location: /system_ordering/public/admin/consumable/sections');
                    exit;
                }
            }

            header('Location: /system_ordering/public/admin/consumable/sections');
            exit;
        }
    }

    public static function editSection()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentRole = $_SESSION['user_data']['role'] ?? null;

        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa edit section.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = $_POST['id'] ?? null;
            $name        = trim($_POST['name'] ?? '');
            $description = $_POST['description'] ?? null;
            $oldImage    = $_POST['old_image'] ?? null;

            if (!$id || $name === '') {
                $_SESSION['errors'][] = "Data edit tidak valid.";
            } else {
                // kirim $_FILES + oldImage ke model
                $ok = SectionModel::update($id, $name, $description, $_FILES, $oldImage);
                if ($ok) {
                    header('Location: /system_ordering/public/admin/consumable/sections');
                    exit;
                }
            }

            header('Location: /system_ordering/public/admin/consumable/sections');
            exit;
        }
    }

    public static function deleteSection()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentRole = $_SESSION['user_data']['role'] ?? null;

        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa hapus section.";
            exit;
        }

        $id = $_GET['id'] ?? null;

        if ($id && is_numeric($id)) {
            SectionModel::delete($id);
        } else {
            $_SESSION['errors'][] = "ID section tidak valid.";
        }

        header('Location: /system_ordering/public/admin/consumable/sections');
        exit;
    }
}
