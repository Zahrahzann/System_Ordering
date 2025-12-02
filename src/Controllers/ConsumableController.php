<?php

namespace App\Controllers;

use App\Models\SectionModel;

class ConsumableController
{
    // List Section (semua role bisa lihat)
    public static function listSection()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $sections = SectionModel::getAll();
        $currentRole = $_SESSION['user_data']['role'] ?? 'customer';

        // Tambahan: handle edit mode (khusus admin)
        $isEditMode = false;
        $editData = null;
        if ($currentRole === 'admin' && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $isEditMode = true;
            $editData = SectionModel::find($_GET['edit']);
        }

        require_once __DIR__ . '/../../views/shared/sections.php';
    }

    // Tambah Section (admin only)
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
            // Proses tambah
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? null;

            SectionModel::create($name, $description);
            header('Location: /system_ordering/public/admin/consumable/sections');
            exit;
        } else {
            // GET → tampilkan form create
            require_once __DIR__ . '/../../views/shared/section_form.php';
        }
    }

    // Edit Section (admin only)
    public static function editSection($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentRole = $_SESSION['user_data']['role'] ?? null;

        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa edit section.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Proses update
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? null;

            SectionModel::update($id, $name, $description);
            header('Location: /system_ordering/public/admin/consumable/sections');
            exit;
        } else {
            // GET → tampilkan form edit
            $section = SectionModel::find($id);
            require_once __DIR__ . '/../../views/shared/section_form.php';
        }
    }

    // Hapus Section (admin only)
    public static function deleteSection($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentRole = $_SESSION['user_data']['role'] ?? null;

        if ($currentRole !== 'admin') {
            http_response_code(403);
            echo "Forbidden: hanya admin yang bisa hapus section.";
            exit;
        }

        // Delete via GET (konfirmasi sudah di view)
        SectionModel::delete($id);
        header('Location: /system_ordering/public/admin/consumable/sections');
        exit;
    }
}
