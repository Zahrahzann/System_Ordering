<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <title>Laporan Consumable</title>
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

                    <!-- Header -->
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-boxes mr-2"></i>Laporan Consumable
                        </h1>
                        <p class="page-subtitle">
                            <i class="fas fa-info-circle mr-1"></i>Statistik qty & biaya per section
                        </p>
                    </div>

                    <!-- Filter -->
                    <?php if ($currentRole === 'admin' || $currentRole === 'spv'): ?>
                        <div class="filter-card">
                            <form method="GET" class="form-inline">
                                <label class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>Tahun</label>
                                <select name="year" class="form-control mr-3">
                                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                        <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label class="mr-2"><i class="fas fa-calendar mr-1"></i>Bulan</label>
                                <select name="month" class="form-control mr-3">
                                    <option value="">Bulan Ini</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>>
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

                    <!-- Stats Summary per Section -->
                    <?php if ($currentRole === 'admin' && !empty($reportData)):
                        // Rekap per section
                        $sectionStats = [];
                        foreach ($reportData as $row) {
                            $sid = $row['section_id'];
                            if (!isset($sectionStats[$sid])) {
                                $sectionStats[$sid] = [
                                    'name' => $row['section_name'],
                                    'qty' => 0,
                                    'inhouse' => 0,
                                    'maker' => 0
                                ];
                            }
                            $qty = (int)$row['qty'];
                            $sectionStats[$sid]['qty'] += $qty;
                            $sectionStats[$sid]['inhouse'] += $qty * (float)$row['inhouse_price'];
                            $sectionStats[$sid]['maker']   += $qty * (float)$row['maker_price'];
                        }
                    ?>
                        <div class="row stats-row">
                            <?php foreach ($sectionStats as $stat):
                                $benefit = $stat['maker'] - $stat['inhouse'];
                                $benefitClass = $benefit < 0 ? 'text-danger' : 'text-success'; ?>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="stat-card">
                                        <div class="stat-label"><?= htmlspecialchars($stat['name']) ?></div>
                                        <div class="stat-value">Qty: <?= $stat['qty'] ?></div>
                                        <div class="stat-subvalue">
                                            Inhouse: Rp <?= number_format($stat['inhouse'], 0, ',', '.') ?><br>
                                            Maker: Rp <?= number_format($stat['maker'], 0, ',', '.') ?><br>
                                            Benefit: <span class="<?= $benefitClass ?>">
                                                Rp <?= number_format(abs($benefit), 0, ',', '.') ?>

                                            </span>
                                        </div>
                                        <i class="fas fa-box stat-icon"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Table hanya untuk Admin -->
                    <?php if ($currentRole === 'admin'): ?>
                        <?php if (empty($reportData)): ?>
                            <div class="empty-state text-center">
                                <i class="fas fa-box-open fa-4x mb-3"></i>
                                <h4>Tidak ada data Consumable</h4>
                                <p class="text-muted">Belum ada data consumable untuk periode yang dipilih</p>
                            </div>
                        <?php else: ?>
                            <div class="table-card">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-layer-group mr-1"></i>Section</th>
                                                <th><i class="fas fa-sort-numeric-up mr-1"></i>Total Qty</th>
                                                <th><i class="fas fa-coins mr-1"></i>Total Inhouse</th>
                                                <th><i class="fas fa-hand-holding-usd mr-1"></i>Total Maker</th>
                                                <th><i class="fas fa-balance-scale mr-1"></i>Benefit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Rekap per section
                                            $sectionStats = [];
                                            foreach ($reportData as $row) {
                                                $sid = $row['section_id'];
                                                if (!isset($sectionStats[$sid])) {
                                                    $sectionStats[$sid] = [
                                                        'name' => $row['section_name'],
                                                        'qty' => 0,
                                                        'inhouse' => 0,
                                                        'maker' => 0
                                                    ];
                                                }
                                                $qty = (int)$row['qty'];
                                                $sectionStats[$sid]['qty'] += $qty;
                                                $sectionStats[$sid]['inhouse'] += $qty * (float)$row['inhouse_price'];
                                                $sectionStats[$sid]['maker']   += $qty * (float)$row['maker_price'];
                                            }

                                            $grandQty = $grandInhouse = $grandMaker = 0;
                                            foreach ($sectionStats as $stat):
                                                $benefit = $stat['maker'] - $stat['inhouse'];
                                                $benefitClass = $benefit < 0 ? 'text-danger' : 'text-success';
                                                $grandQty += $stat['qty'];
                                                $grandInhouse += $stat['inhouse'];
                                                $grandMaker   += $stat['maker'];
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($stat['name']) ?></td>
                                                    <td><?= $stat['qty'] ?></td>
                                                    <td>Rp <?= number_format($stat['inhouse'], 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($stat['maker'], 0, ',', '.') ?></td>
                                                    <td class="<?= $benefitClass ?>">
                                                        Rp <?= number_format(abs($benefit), 0, ',', '.') ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <?php $grandBenefit = $grandMaker - $grandInhouse; ?>
                                            <tr>
                                                <th>Grand Total</th>
                                                <th><?= $grandQty ?></th>
                                                <th>Rp <?= number_format($grandInhouse, 0, ',', '.') ?></th>
                                                <th>Rp <?= number_format($grandMaker, 0, ',', '.') ?></th>
                                                <th class="<?= $grandBenefit < 0 ? 'text-danger' : 'text-success' ?>">
                                                    Rp <?= number_format(abs($grandBenefit), 0, ',', '.') ?>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Grafik Consumable --> <?php if (!empty($reportData) && is_array($reportData)): ?> <div class="chart-card">
                                <div class="chart-header">
                                    <h3 class="chart-title"> <i class="fas fa-chart-bar mr-2"></i> Analisis Consumable Tahun <?= $year ?> </h3>
                                    <p class="chart-subtitle"> Total biaya inhouse, vendor, dan benefit per section </p>
                                </div> <canvas id="consumableChart"></canvas>
                            </div>
                            <div class="chart-card" style="margin-top:30px;">
                                <div class="chart-header">
                                    <h3 class="chart-title"> <i class="fas fa-chart-bar mr-2"></i> Grand Total Consumable Tahun <?= $year ?> </h3>
                                    <p class="chart-subtitle"> Ringkasan total inhouse, vendor, benefit, dan kumulatif benefit keseluruhan </p>
                                </div> <canvas id="grandChart"></canvas>
                            </div> <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor JS -->
    <script src="<?= htmlspecialchars($basePath) ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= htmlspecialchars($basePath) ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars($basePath) ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= htmlspecialchars($basePath) ?>/assets/js/sb-admin-2.min.js"></script>

    <!-- Chart.js-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        const rawData = <?= json_encode($reportData ?? []) ?>;
        const grandData = <?= json_encode($grandChartData ?? []) ?>;

        // Grafik per section
        new Chart(document.getElementById('consumableChart'), {
            type: 'bar',
            data: {
                labels: rawData.map(d => d.section_name),
                datasets: [{
                        label: 'Total Inhouse',
                        data: rawData.map(d => d.total_inhouse),
                        backgroundColor: 'rgba(231,74,59,0.8)'
                    },
                    {
                        label: 'Total Maker',
                        data: rawData.map(d => d.total_maker),
                        backgroundColor: 'rgba(54,185,204,0.8)'
                    },
                    {
                        label: 'Benefit',
                        data: rawData.map(d => d.benefit),
                        backgroundColor: rawData.map(d => d.benefit < 0 ? 'rgba(231,74,59,0.8)' : 'rgba(28,200,138,0.8)')
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Consumable Report per Section'
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#333',
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        formatter: val => {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + ' Jt';
                            if (val >= 1000) return (val / 1000).toFixed(1) + ' K';
                            return val;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Grafik Grand Total per bulan + kumulatif
        new Chart(document.getElementById('grandChart'), {
            type: 'bar',
            data: {
                labels: grandData.labels, // contoh: ['Jan','Feb',...]
                datasets: [{
                        label: 'Total Inhouse',
                        data: grandData.inhouse, // array per bulan
                        backgroundColor: 'rgba(231,74,59,0.8)',
                        yAxisID: 'yCost'
                    },
                    {
                        label: 'Total Maker',
                        data: grandData.maker,
                        backgroundColor: 'rgba(54,185,204,0.8)',
                        yAxisID: 'yCost'
                    },
                    {
                        label: 'Benefit',
                        data: grandData.benefit,
                        backgroundColor: grandData.benefit.map(val => val < 0 ? 'rgba(231,74,59,0.8)' : 'rgba(28,200,138,0.8)'),
                        yAxisID: 'yCost'
                    },
                    {
                        label: 'Cumulative Benefit',
                        type: 'line',
                        data: grandData.cumulative,
                        borderColor: 'rgba(0,0,0,1)',
                        backgroundColor: 'rgba(0,0,0,0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(0,0,0,1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        yAxisID: 'yCost'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Grand Total Benefit Overview'
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#333',
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        formatter: val => {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + ' Jt';
                            if (val >= 1000) return (val / 1000).toFixed(1) + ' K';
                            return val;
                        }
                    }
                },
                scales: {
                    yCost: {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Benefit (Rp)'
                        },
                        beginAtZero: true
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>

</body>

</html>