<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\WorkOrderCostModel;
use App\Models\MachineRateModel;
use App\Models\ManpowerRateModel;

class WorkOrderCostController
{
    /**
     * Admin-only: simpan detail proses + summary WO
     */
    public static function saveCost()
    {
        SessionMiddleware::requireAdminLogin();

        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);

        try {
            // Debug awal
            error_log("saveCost() dipanggil");
            error_log("POST DATA: " . print_r($_POST, true));

            // Ambil data POST
            $orderId         = isset($_POST['order_id']) ? (int)$_POST['order_id'] : null;
            $itemName        = $_POST['item_name'] ?? '';
            $departmentId = $_POST['department_id'] ?? null;
            $customerId      = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $status          = $_POST['status'] ?? 'on_progress';
            $materialCost    = (float)($_POST['material_cost'] ?? 0);
            $vendorPrice     = (float)($_POST['vendor_price_per_pcs'] ?? 0);
            $machineProcess  = trim($_POST['machine_process'] ?? '');
            $machineTime     = (int)($_POST['machine_time'] ?? 0);
            $manpowerProcess = trim($_POST['manpower_process'] ?? '');
            $manpowerTime    = (int)($_POST['manpower_time'] ?? 0);

            // Validasi minimal
            if (!$orderId || !$machineProcess || !$manpowerProcess) {
                throw new \RuntimeException("Data tidak lengkap. Cek kembali input form.");
            }

            // Ambil rate dari tabel machine_rates & manpower_rates
            $machineRate  = MachineRateModel::getRateByProcess($machineProcess);
            $manpowerRate = ManpowerRateModel::getRateByProcess($manpowerProcess);

            error_log("Rate mesin: $machineRate, Rate tenaga kerja: $manpowerRate");

            // Simpan detail proses ke workorder_processes
            error_log("Memanggil saveFullProcess()");
            WorkOrderCostModel::saveFullProcess(
                $orderId,
                $machineProcess,
                $machineTime,
                $machineRate,
                $manpowerProcess,
                $manpowerTime,
                $manpowerRate,
                $materialCost,
                $vendorPrice
            );
            error_log("saveFullProcess selesai");

            // Simpan summary ke workorder_costs
            error_log("Memanggil saveWorkOrderCost()");
            WorkOrderCostModel::saveWorkOrderCost(
                $orderId,
                $itemName,
                $departmentId,
                $customerId,
                $status,
                $materialCost,
                $vendorPrice,
                $machineRate,
                $machineTime,
                $manpowerRate,
                $manpowerTime
            );
            error_log("saveWorkOrderCost selesai");

            // Redirect ke laporan
            $year = date('Y');
            error_log("Redirect ke laporan tahun $year");
            self::redirect("/system_ordering/public/admin/history");
        } catch (\Throwable $e) {
            error_log(" ERROR di saveCost(): " . $e->getMessage());
            echo "<!DOCTYPE html><html><body>";
            echo "<h2 style='color:red;'> ERROR saat saveCost</h2>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "</body></html>";
            exit;
        }
    }

    /**
     * Laporan bulanan WO
     */
    public static function showMonthlyReport()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        SessionMiddleware::requireLogin();

        $role        = $_SESSION['user_data']['role'] ?? 'customer';
        $department  = $_SESSION['user_data']['department_id'] ?? null;
        $customerId  = $_SESSION['user_data']['customer_id'] ?? null;

        $year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;

        // Ambil data di workorder_costs
        $reportData = WorkOrderCostModel::getSummaryReport($year, $month);


        // Khusus SPV, filter berdasarkan departemen
        if ($role === 'spv' && $department) {
            $reportData = array_filter($reportData, function ($row) use ($department) {
                return isset($row['department_id']) && $row['department_id'] === $department;
            });
        }

        $basePath    = '/system_ordering/public';
        $currentRole = $role;

        require_once __DIR__ . '/../../views/shared/workorder_report.php';
    }

    public static function showSummaryReport()
    {
        SessionMiddleware::requireAdminLogin();

        $year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;

        $reportData = WorkOrderCostModel::getSummaryReport($year, $month);

        require_once __DIR__ . '/../../views/admin/summary_report.php';
    }

    public static function redirect(string $url)
    {
        header("Location: $url");
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}
