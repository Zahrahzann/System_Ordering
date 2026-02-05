<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\UserManagementModel;

class UserManagementController
{
    /**
     * Menampilkan halaman daftar SPV (dengan filter)
     */
    public static function listSpv()
    {
        SessionMiddleware::requireAdminLogin();

        $selectedDept = $_GET['department_id'] ?? null;
        $searchQuery  = $_GET['search'] ?? null;

        $users       = UserManagementModel::getAllSpv($selectedDept, $searchQuery);
        $departments = UserManagementModel::getAllDepartments();
        $title       = "Manage Supervisors";

        require_once __DIR__ . '/../../views/admin/manage/spv.php';
    }

    /**
     * Menampilkan halaman daftar Customer (dengan filter)
     */
    public static function listCustomers()
    {
        SessionMiddleware::requireAdminLogin();

        $selectedDept = $_GET['department_id'] ?? null;
        $searchQuery  = $_GET['search'] ?? null;

        $users       = UserManagementModel::getAllCustomers($selectedDept, $searchQuery);
        $departments = UserManagementModel::getAllDepartments();
        $title       = "Manage Customers";

        require_once __DIR__ . '/../../views/admin/manage/customer.php';
    }

    /**
     * Hapus Customer
     */
    public static function deleteCustomer()
    {
        SessionMiddleware::requireAdminLogin();

        $id = $_GET['id'] ?? null;
        if (!empty($id)) {
            UserManagementModel::deleteCustomerById($id);
        }

        // redirect ke halaman manage customer (pakai prefix /system_ordering/public)
        header('Location: /system_ordering/public/admin/manage/customer');
        exit;
    }

    /**
     * Hapus Supervisor (SPV)
     */
    public static function deleteSpv()
    {
        SessionMiddleware::requireAdminLogin();

        $id = $_GET['id'] ?? null;
        if (!empty($id)) {
            UserManagementModel::deleteSpvById($id);
        }

        // redirect ke halaman manage spv (pakai prefix /system_ordering/public)
        header('Location: /system_ordering/public/admin/manage/spv');
        exit;
    }

    
}
