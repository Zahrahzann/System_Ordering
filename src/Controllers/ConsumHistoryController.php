<?php

namespace App\Controllers;

use App\Models\ConsumHistoryModel;
use App\Middleware\SessionMiddleware;

class ConsumHistoryController
{
    public static function showHistory()
    {
        // pastikan session aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['user_data']['role'] ?? 'customer';

        // hanya admin & customer
        if ($role === 'customer') {
            SessionMiddleware::requireCustomerLogin();
        } elseif ($role === 'admin') {
            SessionMiddleware::requireAdminLogin();
        } elseif ($role === 'spv') {
            SessionMiddleware::requireSpvLogin();
        } else {
            http_response_code(403);
            echo "Akses ditolak. Halaman history hanya untuk admin dan customer.";
            exit;
        }

        // kumpulkan filter dari query string
        $filters = [
            'q'          => $_GET['q'] ?? null,
            'section'    => $_GET['section'] ?? null,
            'type'       => $_GET['type'] ?? null,
            'item'       => $_GET['item'] ?? null,
            'customer'   => ($role === 'admin') ? ($_GET['customer'] ?? null) : null,
            'department' => ($role === 'admin') ? ($_GET['department'] ?? null) : null,
            'line'       => ($role === 'admin') ? ($_GET['line'] ?? null) : null,
            'month'      => $_GET['month'] ?? null,
            'year'       => $_GET['year'] ?? null,
        ];

        // ambil data history sesuai role + filter
        $orders   = ConsumHistoryModel::getHistoryFiltered($role, $filters);
        $sections = ConsumHistoryModel::getAllSections();
        $types    = ConsumHistoryModel::getAllProductTypes(
            $filters['section'] ? (int)$filters['section'] : null
        );
        $items    = ConsumHistoryModel::getAllProductItems(
            $filters['type'] ? (int)$filters['type'] : null
        );
        $departments = ConsumHistoryModel::getAllDepartments();

        $currentRole = $role;
        $basePath    = '/system_ordering/public';

        include __DIR__ . '/../../views/shared/consum-history.php';
    }
}
