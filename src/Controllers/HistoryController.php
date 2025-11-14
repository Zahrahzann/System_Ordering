<?php

namespace App\Controllers;

use App\Models\HistoryModel;
use App\Middleware\SessionMiddleware;

class HistoryController
{
    /**
     * Menampilkan halaman riwayat pesanan (shared untuk admin, spv, customer)
     */
    public static function showHistoryPage()
    {
        SessionMiddleware::requireLogin();

        $role = $_SESSION['user_data']['role'];
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;

        $items = [];
        $chartData = [];
        $availableYears = range(date('Y'), date('Y') - 5);

        switch ($role) {
            case 'admin':
                $items = HistoryModel::getHistoryItems(null, $year, $month);
                break;

            case 'spv':
                $departmentId = $_SESSION['user_data']['department_id'];
                $items = HistoryModel::getHistoryItems($departmentId, $year, $month);
                break;

            case 'customer':
                $customerId = $_SESSION['user_data']['id'];
                $items = HistoryModel::getHistoryItemsByCustomer($customerId, $year, $month);
                break;

            default:
                die('Role tidak dikenali.');
        }

        require_once __DIR__ . '/../../views/shared/history_pesanan.php';
    }

    /**
     * Fitur "Pesan Lagi" untuk customer
     */
    public static function reorderItem($itemId)
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'];

        $item = HistoryModel::findItemById($itemId);
        if (!$item || $item['customer_id'] != $customerId) {
            die('Item tidak valid atau bukan milik Anda.');
        }

        $success = HistoryModel::addItemToCart($customerId, $item);

        $_SESSION['flash_message'] = $success
            ? 'Item berhasil ditambahkan kembali ke keranjang.'
            : 'Item sudah ada di keranjang Anda.';

        header('Location: /system_ordering/public/customer/cart');
        exit;
    }
}
