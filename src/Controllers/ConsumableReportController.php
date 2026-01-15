<?php

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Models\ConsumableReportModel;
use App\Models\SectionModel;
use App\Models\ProductItemModel;

class ConsumableReportController
{
    // Tampilkan form input qty + report
    public static function showReport(): void
    {
        SessionMiddleware::requireAdminLogin();

        $month = isset($_GET['month']) && $_GET['month'] !== ''
            ? (int)$_GET['month']
            : (int)date('n');
        $year  = isset($_GET['year']) && $_GET['year'] !== ''
            ? (int)$_GET['year']
            : (int)date('Y');

        // Ambil semua section
        $sections   = SectionModel::getAll();
        // Ambil data report per bulan (per section)
        $reportData = ConsumableReportModel::getReport(null, $year);

        // Gabungkan supaya semua section tetap muncul
        $reportMap = [];
        foreach ($reportData as $row) {
            $key = $row['section_id'] . '-' . $row['month'];
            $reportMap[$key][] = $row;
        }

        // Hitung benefit per section per bulan
        $sectionBenefitMonthly = [];
        foreach ($sections as $section) {
            $sectionName = $section['name'];
            $sectionBenefitMonthly[$sectionName] = array_fill(0, 12, 0);

            for ($m = 1; $m <= 12; $m++) {
                $key = $section['id'] . '-' . $m;
                $benefit = 0;
                if (isset($reportMap[$key])) {
                    foreach ($reportMap[$key] as $row) {
                        $qty = (int)$row['qty'];
                        $inhouse = $qty * (float)$row['inhouse_price'];
                        $maker   = $qty * (float)$row['maker_price'];
                        $benefit += $maker - $inhouse;
                    }
                }
                $sectionBenefitMonthly[$sectionName][$m - 1] = $benefit;
            }
        }

        // Hitung cumulative benefit total per bulan
        $monthlyTotalBenefit = array_fill(0, 12, 0);
        foreach ($sectionBenefitMonthly as $section => $values) {
            foreach ($values as $i => $val) {
                $monthlyTotalBenefit[$i] += $val;
            }
        }
        $cumulativeBenefit = [];
        $running = 0;
        foreach ($monthlyTotalBenefit as $val) {
            $running += $val;
            $cumulativeBenefit[] = $running;
        }

        // =========================
        // Grafik 1: Benefit per Section + Cumulative
        // =========================
        $benefitChartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'datasets' => []
        ];

        $colors = [
            'Presub'   => 'rgba(54,162,235,0.8)',
            'K-Line 3' => 'rgba(255,99,132,0.8)',
            'K-Line 4' => 'rgba(255,159,64,0.8)',
            'K-Line 5' => 'rgba(75,192,192,0.8)',
            'Delivery' => 'rgba(153,102,255,0.8)'
        ];

        foreach ($sectionBenefitMonthly as $section => $monthlyValues) {
            $benefitChartData['datasets'][] = [
                'label' => $section,
                'data' => $monthlyValues,
                'backgroundColor' => $colors[$section] ?? 'rgba(200,200,200,0.8)'
            ];
        }

        // Tambahkan cumulative line
        $benefitChartData['datasets'][] = [
            'label' => 'Total Kumulatif',
            'type' => 'line',
            'data' => $cumulativeBenefit,
            'borderColor' => 'rgba(0,0,0,1)',
            'backgroundColor' => 'rgba(0,0,0,0.1)',
            'borderWidth' => 2,
            'borderDash' => [5, 5],
            'fill' => false,
            'tension' => 0.3,
            'pointRadius' => 4,
            'pointBackgroundColor' => 'rgba(0,0,0,1)'
        ];

        // =========================
        // Grafik 2: Summary Bulanan (Inhouse, Vendor, Benefit, Cumulative)
        // =========================
        $monthlySummary = ConsumableReportModel::getMonthlySummary($year);

        $grandChartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'inhouse' => array_column($monthlySummary, 'total_inhouse'),
            'maker' => array_column($monthlySummary, 'total_maker'),
            'benefit' => array_column($monthlySummary, 'benefit'),
            'cumulative' => array_column($monthlySummary, 'cumulative')
        ];
        $running = 0;
        for ($i = 0; $i < 12; $i++) {
            $val = $monthlySummary[$i]['benefit'] ?? 0;
            $running += $val;
            $grandChartData['cumulative'][$i] = $running;
        }

        // Kirim ke view
        require_once __DIR__ . '/../../views/shared/consumable_report.php';
    }

    // Simpan qty input bulanan
    public static function saveReport(): void
    {
        SessionMiddleware::requireAdminLogin();

        $month = (int)($_POST['month'] ?? date('n'));
        $year  = (int)($_POST['year'] ?? date('Y'));

        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $itemId => $qty) {
                $item = ProductItemModel::find($itemId);
                if (!$item) continue;

                ConsumableReportModel::saveQty(
                    $item['section_id'],
                    $item['product_type_id'],
                    $itemId,
                    $month,
                    $year,
                    (int)$qty
                );
            }
        }

        self::redirect("/system_ordering/public/admin/consumable/report?month=$month&year=$year");
    }

    private static function redirect(string $url): void
    {
        header("Location: $url");
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}
