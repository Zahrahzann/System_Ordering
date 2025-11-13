<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;

class AdminController
{
    /**
     * Menampilkan halaman dashboard Admin.
     * DILINDUNGI OLEH MIDDLEWARE.
     */
    public static function showDashboard()
    {
        // PENTING: Panggil middleware untuk cek login admin
        SessionMiddleware::requireAdminLogin();

        $userData = $_SESSION['user_data'];

        // Jika lolos, baru tampilkan halaman dashboard
        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }

    /**
     * Menampilkan halaman login Admin.
     */
    public static function showLoginPage()
    {
        // KOREKSI TYPO: 'vies' diubah menjadi 'views'
        require_once __DIR__ . '/../../views/admin/login_admin.php';
    }
}
