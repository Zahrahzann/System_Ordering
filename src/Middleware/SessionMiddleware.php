<?php

namespace App\Middleware;

class SessionMiddleware
{
    public static function requireLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Cukup cek apakah ada data pengguna di session
        if (!isset($_SESSION['user_data'])) {
            // Jika tidak ada siapa pun yang login, lempar ke login customer sebagai default
            // KOREKSI: Redirect harus ke PUBLIC
            header('Location: /system_ordering/public/customer/login');
            exit;
        }
    }

    /**
     * Memastikan hanya customer yang sudah login yang bisa mengakses.
     * Jika belum login, akan diarahkan ke halaman login customer.
     */
    public static function requireCustomerLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Pastikan role-nya adalah 'customer'
        if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'customer') {
            // KOREKSI: Redirect harus ke PUBLIC
            header('Location: /system_ordering/public/customer/login');
            exit;
        }
    }

    /**
     * FUNGSI BARU: Memastikan hanya SPV yang sudah login yang bisa mengakses.
     * Jika belum login atau role-nya bukan SPV, akan diarahkan ke halaman login SPV.
     */
    public static function requireSpvLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Cek apakah user ada di session DAN apakah rolenya 'spv'
        if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'spv') {
            // KOREKSI: Redirect harus ke PUBLIC
            header('Location: /system_ordering/public/spv/login');
            exit;
        }
    }

    public static function requireAdminLogin()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
            // KOREKSI: Redirect harus ke PUBLIC
            header('Location: /system_ordering/public/admin/login');
            exit;
        }
    }
}
