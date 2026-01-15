<?php

namespace App\Controllers;

use App\Models\ConsumableReportModel;
use App\Helpers\ExportHelper;

class DetailReportController
{
    // Tampilkan laporan detail
    public static function show($year)
    {
        $data = ConsumableReportModel::getYearlyBreakdown($year);
        require_once __DIR__ . '/../../views/shared/detail_reportcons.php';
    }

    // Export ke Excel
    public static function exportExcel($year)
    {
        $data = ConsumableReportModel::getYearlyBreakdown($year);

        $headers = [
            "NO",
            "SLIPPER NAME",
            "PAD NAME",
            "HARGA INHOUSE (ME)",
            "HARGA MAKER (VENDOR)",
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
            "TOTAL PCS",
            "TOTAL HARGA ME",
            "TOTAL HARGA MAKER",
            "REDUCE COST"
        ];

        $rows = [];
        $no = 1;
        foreach ($data as $row) {
            $rows[] = [
                $no++,
                $row['product_type'],
                $row['item_name'],
                $row['inhouse_price'],
                $row['maker_price'],
                $row['month'] == 1 ? $row['total_qty'] : 0,
                $row['month'] == 2 ? $row['total_qty'] : 0,
                $row['month'] == 3 ? $row['total_qty'] : 0,
                $row['month'] == 4 ? $row['total_qty'] : 0,
                $row['month'] == 5 ? $row['total_qty'] : 0,
                $row['month'] == 6 ? $row['total_qty'] : 0,
                $row['month'] == 7 ? $row['total_qty'] : 0,
                $row['month'] == 8 ? $row['total_qty'] : 0,
                $row['month'] == 9 ? $row['total_qty'] : 0,
                $row['month'] == 10 ? $row['total_qty'] : 0,
                $row['month'] == 11 ? $row['total_qty'] : 0,
                $row['month'] == 12 ? $row['total_qty'] : 0,
                $row['total_qty'],
                $row['total_me'],
                $row['total_maker'],
                $row['total_benefit'],
            ];
        }

        ExportHelper::exportToExcel("laporan_detail_consumable_{$year}.csv", $headers, $rows);
    }
}
