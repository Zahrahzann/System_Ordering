<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\HistoryModel;
use App\Models\OrderModel;

class AdminController
{
    /**
     * Menampilkan halaman dashboard Admin.
     */

    public static function showDashboard()
    {
        SessionMiddleware::requireAdminLogin();

        $userData = $_SESSION['user_data'];
        $year = date('Y');

        // Ambil data WO masuk dan WO completed
        $totalInData     = OrderModel::getMonthlyWoInData($year);
        $completedData   = OrderModel::getMonthlyWoCompletedData($year);
        $pendingData     = OrderModel::getMonthlyWoPendingData($year);
        $progressData    = OrderModel::getMonthlyWoOnProgress($year);
        $finishData      = OrderModel::getMonthlyWoFinishData($year);

        $qtyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $qtyData[] = [
                'month'           => $m,
                'total_in'        => $totalInData[$m] ?? 0,
                'total_completed' => $completedData[$m] ?? 0,
                'total_pending'   => $pendingData[$m] ?? 0,
                'total_onProgress'=> $progressData[$m] ?? 0,
                'total_finish'    => $finishData[$m] ?? 0
            ];
        }
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
