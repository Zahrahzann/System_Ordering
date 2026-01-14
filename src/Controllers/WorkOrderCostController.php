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

        try {
            error_log("saveCost() dipanggil");
            error_log("POST DATA: " . print_r($_POST, true));

            // Ambil data POST
            $orderId      = isset($_POST['order_id']) ? (int)$_POST['order_id'] : null;
            $itemName     = $_POST['item_name'] ?? '';
            $departmentId = $_POST['department_id'] ?? null;
            $customerId   = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $status       = $_POST['status'] ?? 'on_progress';
            $materialCost = (float)($_POST['material_cost'] ?? 0);
            $vendorPrice  = (float)($_POST['vendor_price_per_pcs'] ?? 0);

            if (!$orderId) {
                throw new \RuntimeException("Order ID tidak boleh kosong.");
            }

            // --- Hitung total machine cost (multi process) ---
            $machineTotal = 0.0;

            if (!empty($_POST['machine_process']) && is_array($_POST['machine_process'])) {
                foreach ($_POST['machine_process'] as $idx => $proc) {
                    $proc = trim((string)$proc);
                    $time = (int)($_POST['machine_time'][$idx] ?? 0);

                    if ($proc === '' || $time <= 0) {
                        continue;
                    }

                    $rate = (float)MachineRateModel::getRateByProcess($proc);
                    $cost = $rate * $time;
                    $machineTotal += $cost;

                    WorkOrderCostModel::saveFullProcess(
                        $orderId,
                        $proc,
                        $time,
                        $rate,
                        '',
                        0,
                        0.0,
                        $materialCost,
                        $vendorPrice
                    );
                }
            }

            // --- Hitung total manpower cost (multi process) ---
            $manpowerTotal = 0.0;

            if (!empty($_POST['manpower_process']) && is_array($_POST['manpower_process'])) {
                foreach ($_POST['manpower_process'] as $idx => $proc) {
                    $proc = trim((string)$proc);
                    $time = (int)($_POST['manpower_time'][$idx] ?? 0);

                    if ($proc === '' || $time <= 0) {
                        continue;
                    }

                    $rate = (float)ManpowerRateModel::getRateByProcess($proc);
                    $cost = $rate * $time;
                    $manpowerTotal += $cost;

                    WorkOrderCostModel::saveFullProcess(
                        $orderId,
                        '', // machine kosong
                        0,
                        0.0,
                        $proc,
                        $time,
                        $rate,
                        $materialCost,
                        $vendorPrice
                    );
                }
            }

            // --- Simpan summary ke workorder_costs ---
            WorkOrderCostModel::saveWorkOrderCost(
                $orderId,
                $itemName,
                $departmentId,
                $customerId,
                $status,
                $materialCost,
                $vendorPrice,
                $machineTotal,
                $manpowerTotal
            );

            self::redirect("/system_ordering/public/admin/history");
        } catch (\Throwable $e) {
            error_log("ERROR di saveCost(): " . $e->getMessage());
            echo "<!DOCTYPE html><html><body>";
            echo "<h2 style='color:red;'>ERROR saat saveCost</h2>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "</body></html>";
            exit;
        }
    }

    // --- Report functions tetap sama ---
    public static function showMonthlyReport()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        SessionMiddleware::requireLogin();

        $role        = $_SESSION['user_data']['role'] ?? 'customer';
        $department  = $_SESSION['user_data']['department_id'] ?? null;
        $customerId  = $_SESSION['user_data']['customer_id'] ?? null;

        $month = isset($_GET['month']) && $_GET['month'] !== ''
            ? (int)$_GET['month']
            : (int)date('n');   // default bulan sekarang
        $year  = isset($_GET['year']) && $_GET['year'] !== ''
            ? (int)$_GET['year']
            : (int)date('Y');   // default tahun sekarang

        $reportData = WorkOrderCostModel::getSummaryReport($year, $month);

        if ($role === 'spv' && $department) {
            $reportData = array_filter($reportData, fn($row) => isset($row['department_id']) && $row['department_id'] === $department);
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
