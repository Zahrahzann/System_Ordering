<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flash error message
if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php
$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? null;
$reportData  = $reportData ?? [];
$year        = $year ?? date('Y');
$month       = $month ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Work Order</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/shared/report_wo.css?v=<?= time() ?>" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <!-- Debug Section -->
                    <?php
                    // echo "<pre style='background:#222;color:#0f0;padding:10px;'>";
                    // echo "DEBUG VIEW workorder_report.php\n";
                    // echo "currentRole: ";
                    // var_dump($currentRole ?? null);
                    // echo "year: ";
                    // var_dump($year ?? null);
                    // echo "month: ";
                    // var_dump($month ?? null);
                    // echo "reportData: ";
                    // var_dump($reportData ?? null);
                    // echo "</pre>";
                    // 
                    ?>

                    <!-- Header -->
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-chart-line mr-2"></i>Laporan Work Order
                        </h1>
                        <p class="page-subtitle">
                            <?php if (isset($currentRole) && $currentRole === 'admin'): ?>
                                <i class="fas fa-info-circle mr-1"></i>Laporan lengkap WO + input biaya
                            <?php elseif (isset($currentRole) && $currentRole === 'spv'): ?>
                                <i class="fas fa-info-circle mr-1"></i>Laporan WO untuk departemen Anda
                            <?php else: ?>
                                <i class="fas fa-info-circle mr-1"></i>Laporan WO untuk pesanan Anda
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Filter (Admin & SPV) -->
                    <?php if (isset($currentRole) && $currentRole !== 'customer'): ?>
                        <div class="filter-card">
                            <form method="GET" class="form-inline">
                                <label class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>Tahun</label>
                                <select name="year" class="form-control mr-3">
                                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                        <option value="<?= $y ?>" <?= (isset($year) && $y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label class="mr-2"><i class="fas fa-calendar mr-1"></i>Bulan</label>
                                <select name="month" class="form-control mr-3">
                                    <option value="">Semua</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= (isset($month) && $m == $month) ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-filter">
                                    <i class="fas fa-filter mr-1"></i> Filter Data
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Stats Summary hanya untuk Admin -->
                    <?php if ($currentRole === 'admin' && !empty($reportData) && is_array($reportData)):
                        // Jika reportData berasal dari getReportDirect()
                        $hasDirectFields = isset($reportData[0]['cost_machine']) && isset($reportData[0]['total_process_cost']);

                        if ($hasDirectFields) {
                            $totalMachine   = array_sum(array_column($reportData, 'cost_machine'));
                            $totalManpower  = array_sum(array_column($reportData, 'cost_manpower'));
                            $totalProcess   = array_sum(array_column($reportData, 'total_process_cost'));
                        } else {
                            // Jika reportData berasal dari getSummaryReport()
                            $totalMachine   = array_sum(array_column($reportData, 'cost_machine_tool_electric'));
                            $totalManpower  = array_sum(array_column($reportData, 'cost_manpower'));
                            $totalProcess   = array_sum(array_column($reportData, 'cost_inhouse_total'));
                        }

                        $totalOrders = count($reportData);
                    ?>
                        <div class="row stats-row">
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card">
                                    <div class="stat-label">Total Orders</div>
                                    <div class="stat-value"><?= $totalOrders ?></div>
                                    <i class="fas fa-clipboard-list stat-icon"></i>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card warning">
                                    <div class="stat-label">Total Machine Cost</div>
                                    <div class="stat-value">Rp <?= number_format($totalMachine, 0, ',', '.') ?></div>
                                    <i class="fas fa-cogs stat-icon"></i>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card info">
                                    <div class="stat-label">Total Manpower Cost</div>
                                    <div class="stat-value">Rp <?= number_format($totalManpower, 0, ',', '.') ?></div>
                                    <i class="fas fa-users stat-icon"></i>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="stat-card success">
                                    <div class="stat-label">Total Cost Inhouse</div>
                                    <div class="stat-value">Rp <?= number_format($totalProcess, 0, ',', '.') ?></div>
                                    <i class="fas fa-chart-line stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Table hanya untuk Admin -->
                    <?php if ($currentRole === 'admin'): ?>
                        <?php if (empty($reportData)): ?>
                            <div class="empty-state text-center">
                                <i class="fas fa-box-open fa-4x mb-3"></i>
                                <h4>Tidak ada data WO</h4>
                                <p class="text-muted">Belum ada data work order untuk periode yang dipilih</p>
                            </div>
                        <?php else: ?>
                            <div class="table-card">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-box mr-1"></i>Item</th>
                                                <th><i class="fas fa-sort-numeric-up mr-1"></i>Qty</th>
                                                <th><i class="fas fa-coins mr-1"></i>Cost Material</th>
                                                <th><i class="fas fa-users mr-1"></i>Cost Manpower</th>
                                                <th><i class="fas fa-bolt mr-1"></i>Cost Machine</th>
                                                <th><i class="fas fa-percentage mr-1"></i>Overhead (10%)</th>
                                                <th><i class="fas fa-chart-line mr-1"></i>Cost/Pcs</th>
                                                <th><i class="fas fa-calculator mr-1"></i>Total Cost Inhouse</th>
                                                <th><i class="fas fa-hand-holding-usd mr-1"></i>Total Vendor Price</th>
                                                <th><i class="fas fa-balance-scale mr-1"></i>Benefit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reportData as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['item_name'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($row['qty'] ?? '') ?></td>
                                                    <td>Rp <?= number_format($row['cost_material'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['cost_manpower'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['cost_machine_tool_electric'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['overhead'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['cost_per_pcs'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['cost_inhouse_total'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['vendor_price_total'] ?? 0, 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($row['benefit'] ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php // Mapping bulan ke bahasa Indonesia 
                    $bulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    $monthName = '';
                    if (!empty($month)) {
                        $monthName = $bulanIndo[$month] ?? '';
                    }
                    ?>

                    <!-- Grafik -->
                    <?php if (!empty($reportData) && is_array($reportData)): ?>
                        <div class="chart-card">
                            <div class="chart-header">
                                <div>
                                    <h3 class="chart-title">
                                        <i class="fas fa-chart-bar mr-2"></i>
                                        Analisis Work Order Tahun <?= $year ?>
                                    </h3>
                                    <p class="chart-subtitle">
                                        Total biaya inhouse, vendor, dan benefit per bulan sepanjang tahun
                                    </p>
                                </div>
                                <!-- Dropdown Tahun -->
                                <div style="margin-top:10px;">
                                    <label for="yearSelect">Pilih Tahun:</label>
                                    <select id="yearSelect" class="form-control" style="width:150px;display:inline-block;">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                                            $selected = ($y == $year) ? "selected" : "";
                                            echo "<option value='$y' $selected>$y</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <canvas id="woChart"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>




<!-- Vendor JS -->
<script src="<?= htmlspecialchars($basePath) ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= htmlspecialchars($basePath) ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= htmlspecialchars($basePath) ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= htmlspecialchars($basePath) ?>/assets/js/sb-admin-2.min.js"></script>

<!-- Chart.js-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    const rawData = <?= json_encode($reportData ?? []) ?>;
    const yearSelect = document.getElementById('yearSelect');

    function buildChart(selectedYear) {
        const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthlyInhouse = Array(12).fill(0);
        const monthlyVendor = Array(12).fill(0);
        const monthlyBenefit = Array(12).fill(0);

        // Hitung total per bulan sesuai tahun
        rawData.forEach(d => {
            const date = new Date(d.updated_at);
            const year = date.getFullYear();
            const month = date.getMonth();
            if (year == selectedYear) {
                monthlyInhouse[month] += parseFloat(d.cost_inhouse_total || 0);
                monthlyVendor[month] += parseFloat(d.vendor_price_total || 0);
                monthlyBenefit[month] += parseFloat(d.benefit || 0);
            }
        });

        // Hitung kumulatif benefit
        const cumulativeBenefit = monthlyBenefit.reduce((acc, val, idx) => {
            acc.push((acc[idx - 1] || 0) + val);
            return acc;
        }, []);

        const ctx = document.getElementById('woChart');
        if (ctx.chartInstance) ctx.chartInstance.destroy();

        ctx.chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                        label: 'Total Inhouse Cost',
                        data: monthlyInhouse,
                        backgroundColor: 'rgba(231,74,59,0.8)',
                        borderColor: 'rgba(231,74,59,1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Total Vendor Price',
                        data: monthlyVendor,
                        backgroundColor: 'rgba(54,185,204,0.8)',
                        borderColor: 'rgba(54,185,204,1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Total Benefit',
                        data: monthlyBenefit,
                        backgroundColor: 'rgba(28,200,138,0.8)',
                        borderColor: 'rgba(28,200,138,1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Cumulative Benefit',
                        type: 'line',
                        data: cumulativeBenefit,
                        borderColor: 'rgba(246,194,62,1)',
                        backgroundColor: 'rgba(246,194,62,0.1)',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgba(246,194,62,1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#333',
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        formatter: val => val >= 1000000 ? (val / 1000000).toFixed(1) + 'M' : val >= 1000 ? (val / 1000).toFixed(1) + 'K' : val
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Biaya (Rp)'
                        },
                        beginAtZero: true
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    // Inisialisasi chart dengan tahun default
    buildChart(yearSelect.value);

    // Event listener dropdown
    yearSelect.addEventListener('change', function() {
        buildChart(this.value);
    });
</script>

</body>

</html>