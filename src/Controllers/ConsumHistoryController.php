<?php

namespace App\Controllers;

use App\Models\ConsumHistoryModel;
use App\Middleware\SessionMiddleware;

class ConsumHistoryController
{
    public static function showHistory()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $role = $_SESSION['user_data']['role'] ?? 'customer';

        $filters = [
            'q'         => $_GET['q'] ?? null,
            'section'   => $_GET['section'] ?? null,
            'type'      => $_GET['type'] ?? null,
            'item'      => $_GET['item'] ?? null,
            'customer'  => ($role !== 'customer') ? ($_GET['customer'] ?? null) : null,
            'department'=> ($role !== 'customer') ? ($_GET['department'] ?? null) : null,
            'line'      => ($role !== 'customer') ? ($_GET['line'] ?? null) : null,
        ];

        $orders = ConsumHistoryModel::getHistoryFiltered($role, $filters);

        $sections = ConsumHistoryModel::getAllSections();
        $types    = ConsumHistoryModel::getAllProductTypes($filters['section'] ? (int)$filters['section'] : null);
        $items    = ConsumHistoryModel::getAllProductItems($filters['type'] ? (int)$filters['type'] : null);

        $currentRole = $role;
        $basePath = '/system_ordering/public';

        include __DIR__ . '/../views/consum-history.php';
    }
}
