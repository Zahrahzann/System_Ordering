<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\HistoryModel;
use App\Models\OrderModel;
use App\Models\ConsumHistoryModel;

class AdminController
{
    /**
     * Menampilkan halaman dashboard Admin.
     */
    public static function showDashboard()
    {
        SessionMiddleware::requireAdminLogin();

        $userData = $_SESSION['user_data'];

        // Ambil tahun dari dropdown (GET), default tahun sekarang
        $year = $_GET['year'] ?? date('Y');

        // === Data Work Order ===
        $totalInData   = OrderModel::getMonthlyWoInData($year);
        $completedData = OrderModel::getMonthlyWoCompletedData($year);
        $pendingData   = OrderModel::getMonthlyWoPendingData($year);
        $progressData  = OrderModel::getMonthlyWoOnProgress($year);
        $finishData    = OrderModel::getMonthlyWoFinishData($year);

        $qtyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $qtyData[] = [
                'month'            => $m,
                'total_in'         => $totalInData[$m] ?? 0,
                'total_completed'  => $completedData[$m] ?? 0,
                'total_pending'    => $pendingData[$m] ?? 0,
                'total_onProgress' => $progressData[$m] ?? 0,
                'total_finish'     => $finishData[$m] ?? 0
            ];
        }

        // === Data Consumable per section per bulan ===
        $rawData = ConsumHistoryModel::getMonthlyQtyBySection((int)$year);
        // echo "<pre>";
        // print_r($rawData);
        // echo "</pre>";
        // exit;

        // Ambil semua section dari tabel sections, bukan hanya dari hasil query
        $sections = array_column(ConsumHistoryModel::getAllSections(), 'name');

        sort($sections);

        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        // Normalisasi data: setiap bulan punya semua section
        $monthlyData = [];
        foreach (range(1, 12) as $m) {
            $monthlyData[$m] = array_fill_keys($sections, 0);
        }
        foreach ($rawData as $row) {
            $m   = (int)$row['month'];
            $sec = $row['section_name'];
            if (isset($monthlyData[$m][$sec])) {
                $monthlyData[$m][$sec] = (int)$row['total_qty'];
            }
        }

        // Hitung kumulatif total per bulan
        $cumulative = [];
        $total = 0;
        foreach ($monthlyData as $sectionQtys) {
            $sum = array_sum($sectionQtys);
            $total += $sum;
            $cumulative[] = $total;
        }

        // Siapkan dataset untuk view
        $chartMonths   = array_values($monthNames);
        $chartSections = $sections;
        $chartDatasets = [];
        foreach ($sections as $sec) {
            $chartDatasets[$sec] = [];
            foreach ($monthlyData as $sectionQtys) {
                $chartDatasets[$sec][] = $sectionQtys[$sec];
            }
        }

        // ✅ PENTING: Extract semua variabel agar tersedia di view
        extract([
            'userData'       => $userData,
            'year'           => $year,
            'qtyData'        => $qtyData,
            'chartMonths'    => $chartMonths,
            'chartSections'  => $chartSections,
            'chartDatasets'  => $chartDatasets,
            'cumulative'     => $cumulative
        ]);

        // Kirim ke view
        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }

    /**
     * Menampilkan halaman login Admin.
     */
    public static function showLoginPage()
    {
        require_once __DIR__ . '/../../views/admin/login_admin.php';
    }
}
