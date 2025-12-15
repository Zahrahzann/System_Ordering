<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\ConsumHistoryModel;

class ConsumReportController
{
    public static function showReport()
    {
        SessionMiddleware::requireAdminLogin();

        $year  = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;

        $reportData = ConsumHistoryModel::getHistoryItems($year, $month);

        extract([
            'year'       => $year,
            'month'      => $month,
            'reportData' => $reportData
        ]);

        require_once __DIR__ . '/../../views/admin/consum_report.php';
    }

    public static function exportExcel()
    {
        SessionMiddleware::requireAdminLogin();

        $year  = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;

        $data = ConsumHistoryModel::getHistoryItems($year, $month);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=consum_report_{$year}_{$month}.xls");

        echo "Order ID\tProduct\tQty\tSection\tStatus\tTanggal Selesai\n";
        foreach ($data as $row) {
            echo "{$row['order_id']}\t{$row['product_name']}\t{$row['quantity']}\t{$row['section_name']}\t{$row['status']}\t{$row['completed_date']}\n";
        }
        exit;
    }
}
