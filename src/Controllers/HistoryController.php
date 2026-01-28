<?php

namespace App\Controllers;

use App\Models\HistoryModel;
use App\Middleware\SessionMiddleware;
use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class HistoryController
{
    /**
     * Menampilkan halaman riwayat pesanan (shared untuk admin, spv, customer)
     */
    public static function showHistoryPage()
    {
        SessionMiddleware::requireLogin();

        $role  = $_SESSION['user_data']['role'] ?? null;
        if (!$role) {
            die('Role tidak ditemukan di session.');
        }

        $year          = $_GET['year'] ?? date('Y');
        $month         = !empty($_GET['month']) ? (int)$_GET['month'] : null;
        $items         = [];
        $departments   = [];
        $availableYears = range(date('Y'), date('Y') - 5);
        $departmentId  = !empty($_GET['department']) ? (int)$_GET['department'] : null;

        // Default kosong supaya view tidak error
        $machineRates  = [];
        $manpowerRates = [];

        // log setelah variabel diisi
        error_log("Masuk ke HistoryController::showHistoryPage, role=$role, year=$year, month=$month");

        try {
            switch ($role) {
                case 'admin':
                    $items       = HistoryModel::getHistoryItems($departmentId, $year, $month);
                    $departments = HistoryModel::getAllDepartments();

                    $pdo = Database::connect();
                    $machineRates  = $pdo->query("SELECT process_name, price_per_minute FROM machine_rates")->fetchAll(PDO::FETCH_ASSOC);
                    $manpowerRates = $pdo->query("SELECT process_name, price_per_minute FROM manpower_rates")->fetchAll(PDO::FETCH_ASSOC);

                    error_log("Admin: items=" . count($items));
                    break;

                case 'spv':
                    $departmentId = $_SESSION['user_data']['department_id'] ?? null;
                    $items        = HistoryModel::getHistoryItems($departmentId, $year, $month);

                    $pdo = Database::connect();
                    $machineRates  = $pdo->query("SELECT process_name, price_per_minute FROM machine_rates")->fetchAll(PDO::FETCH_ASSOC);
                    $manpowerRates = $pdo->query("SELECT process_name, price_per_minute FROM manpower_rates")->fetchAll(PDO::FETCH_ASSOC);

                    error_log("SPV: items=" . count($items));
                    break;

                case 'customer':
                    $customerId = $_SESSION['user_data']['id'] ?? null;
                    $items      = HistoryModel::getHistoryItemsByCustomer($customerId, $year, $month);

                    error_log("Customer: items=" . count($items));
                    break;

                default:
                    die('Role tidak dikenali.');
            }
        } catch (\Exception $e) {
            error_log("❌ Error load history: " . $e->getMessage());
            echo "<pre>Error load history: " . $e->getMessage() . "</pre>";
        }

        $currentRole = $role;
        $viewPath    = __DIR__ . '/../../views/shared/history_pesanan.php';

        if (file_exists($viewPath)) {
            // Pastikan semua variabel tersedia untuk view
            require $viewPath;
        } else {
            echo "<pre>View history_pesanan.php tidak ditemukan.</pre>";
        }
    }

    /**
     * Fitur "Pesan Lagi" untuk customer
     */
    public static function reorderItem($itemId)
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'];

        $item = HistoryModel::findItemById($itemId);

        if (!$item || $item['customer_id'] != $customerId || $item['item_type'] !== 'work_order') {
            die('Item tidak valid atau bukan Work Order milik Anda.');
        }

        $_SESSION['reorder_item'] = $item;

        header('Location: /system_ordering/public/customer/work_order/form?reorder=1');
        exit;
    }
}
