<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\HistoryModel;
use App\Helpers\ExportHelper;

class WOReportController
{
    /**
     * Menampilkan halaman laporan Work Order (filter tahun/bulan).
     */
    public static function showReport()
    {
        SessionMiddleware::requireAdminLogin();

        $year  = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null; // null = semua bulan

        // Ambil data riwayat WO completed sesuai filter
        $reportData = HistoryModel::getHistoryItems(null, $year, $month);

        // Ambil semua department untuk filter opsional
        $departments = HistoryModel::getAllDepartments();

        extract([
            'year'        => $year,
            'month'       => $month,
            'reportData'  => $reportData,
            'departments' => $departments
        ]);

        require_once __DIR__ . '/../../views/admin/workorder_report.php';
    }

public static function exportExcel()
{
    SessionMiddleware::requireAdminLogin();

    $year  = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? null;

    $data = HistoryModel::getHistoryItems(null, $year, $month);

    // Siapkan header & rows
    $headers = ["Order ID", "Item", "Qty", "Customer", "Department", "Status", "Tanggal Selesai"];
    $rows = [];
    foreach ($data as $row) {
        $rows[] = [
            $row['order_id'],
            $row['item_name'],
            $row['quantity'],
            $row['customer_name'],
            $row['department_name'],
            $row['production_status'],
            $row['completed_date']
        ];
    }

    ExportHelper::exportToExcel("workorder_report_{$year}_{$month}.xls", $headers, $rows);
}

}
