<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\TrackingModel;

class DashboardController
{
    /**
     * Menampilkan halaman dashboard sesuai role
     */
    public static function showDashboard()
    {
        SessionMiddleware::requireLogin();

        $role   = $_SESSION['user_data']['role'] ?? 'customer';
        $userId = $_SESSION['user_data']['id'] ?? null;

        // Ambil stats dari TrackingModel
        $stats = TrackingModel::getDashboardStats($role, $userId);

        // Kirim data ke view dashboard
        require_once __DIR__ . '/../../views/' . $role . '/dashboard.php';
    }
}
