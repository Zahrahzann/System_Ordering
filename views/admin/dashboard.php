<?php
if (!isset($userData)) die('Controller tidak menyediakan data user.');
if (!isset($qtyData) || !is_array($qtyData)) $qtyData = [];

// ✅ FALLBACK untuk variabel chart consumable
if (!isset($chartMonths)) $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
if (!isset($chartSections)) $chartSections = [];
if (!isset($chartDatasets)) $chartDatasets = [];
if (!isset($cumulative)) $cumulative = array_fill(0, 12, 0);
if (!isset($year)) $year = date('Y');

$basePath = '/system_ordering/public';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }

        /* Welcome Section */
        .welcome-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-left: 4px solid #667eea;
        }

        .welcome-section h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .welcome-section p {
            font-size: 16px;
            color: #4a4a4aff;
            margin: 0;
        }

        .welcome-section strong {
            color: #667eea;
            font-weight: 600;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .stat-card.dark {
            border-left-color: #241a84ff;
        }

        .stat-card.blue {
            border-left-color: #62a0fdff;
        }

        .stat-card.gray {
            border-left-color: #3d3d3dff;
        }

        .stat-card.orange {
            border-left-color: #ff9f40;
        }

        .stat-card.green {
            border-left-color: #44c836ff;
        }

        .stat-card-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-card.dark .stat-icon {
            background: rgba(54, 162, 235, 0.1);
            color: #241a84ff;
        }

        .stat-card.blue .stat-icon {
            background: rgba(75, 192, 192, 0.1);
            color: #62a0fdff;
        }

        .stat-card.gray .stat-icon {
            background: rgba(128, 128, 128, 0.1);
            color: #3d3d3dff;
        }

        .stat-card.orange .stat-icon {
            background: rgba(255, 159, 64, 0.1);
            color: #ff9f40;
        }

        .stat-card.green .stat-icon {
            background: rgba(89, 180, 89, 0.1);
            color: #44c836ff;
        }

        .stat-details {
            flex: 1;
        }

        .stat-label {
            font-size: 13px;
            color: #333333ff;
            font-weight: 500;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            line-height: 1;
        }

        /* Chart Card */
        .chart-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .chart-card-header {
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chart-card-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-card-header i {
            color: #667eea;
            font-size: 20px;
        }

        .chart-card-body {
            padding: 30px;
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-bottom: 20px;
        }

        .chart-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #f0f0f0;
        }

        .chart-footer small {
            font-size: 13px;
            color: #666;
            display: block;
            line-height: 1.6;
        }

        .chart-footer code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            color: #667eea;
            font-size: 12px;
        }

        .no-data-message {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-data-message i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 20px 15px;
            }

            .welcome-section {
                padding: 20px;
            }

            .welcome-section h1 {
                font-size: 22px;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-value {
                font-size: 24px;
            }

            .chart-card-body {
                padding: 20px 15px;
            }

            .chart-container {
                height: 280px;
            }
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">

        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column w-100">
            <div id="content">

                <?php include __DIR__ . '/../layout/topbar.php'; ?>

                <div class="container-fluid">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <h1><i class="fas fa-home"></i> Dashboard Admin</h1>
                        <p>Selamat datang, <strong><?= htmlspecialchars($userData['name'] ?? 'Admin') ?></strong></p>
                    </div>

                    <!-- Stats Cards -->
                    <?php
                    $totalWOIn        = array_sum(array_column($qtyData, 'total_in')) ?: 0;
                    $totalWOCompleted = array_sum(array_column($qtyData, 'total_completed')) ?: 0;
                    $totalWOPending   = array_sum(array_column($qtyData, 'total_pending')) ?: 0;
                    $totalWOOnProgress = array_sum(array_column($qtyData, 'total_onProgress')) ?: 0;
                    $totalWOFinish    = array_sum(array_column($qtyData, 'total_finish')) ?: 0;
                    ?>
                    <div class="stats-row">
                        <div class="stat-card dark">
                            <div class="stat-card-content">
                                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                                <div class="stat-details">
                                    <div class="stat-label">Total WO Masuk</div>
                                    <div class="stat-value"><?= number_format($totalWOIn) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card blue">
                            <div class="stat-card-content">
                                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                                <div class="stat-details">
                                    <div class="stat-label">WO Completed</div>
                                    <div class="stat-value"><?= number_format($totalWOCompleted) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card gray">
                            <div class="stat-card-content">
                                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                                <div class="stat-details">
                                    <div class="stat-label">WO Pending</div>
                                    <div class="stat-value"><?= number_format($totalWOPending) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card orange">
                            <div class="stat-card-content">
                                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                                <div class="stat-details">
                                    <div class="stat-label">WO On Progress</div>
                                    <div class="stat-value"><?= number_format($totalWOOnProgress) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card green">
                            <div class="stat-card-content">
                                <div class="stat-icon"><i class="fas fa-check-square"></i></div>
                                <div class="stat-details">
                                    <div class="stat-label">WO Finish</div>
                                    <div class="stat-value"><?= number_format($totalWOFinish) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Card Work Order -->
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h2><i class="fas fa-chart-bar"></i> Report Total Manufacture Work Order</h2>
                        </div>
                        <div class="chart-card-body">
                            <div class="chart-container">
                                <canvas id="qtyBarChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-footer">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Data diambil dari Work Order per bulan: total order yang masuk dan order yang sudah <code>COMPLETED</code>.
                            </small>
                        </div>
                    </div>

                    <!-- Chart Card Consumable -->
                    <div class="chart-card">
                        <div class="chart-card-header d-flex justify-content-between align-items-center">
                            <h2><i class="fas fa-box"></i> ME - Consumable Part <?= htmlspecialchars($year) ?></h2>
                            <!-- Dropdown Tahun -->
                            <form method="get" class="year-dropdown-form">
                                <label for="year" class="me-2">Tahun:</label>
                                <select name="year" id="year" onchange="this.form.submit()">
                                    <?php
                                    $currentYear = date('Y');
                                    // tampilkan dari tahun sekarang mundur 5 tahun
                                    for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                                        $selected = ($year == $y) ? 'selected' : '';
                                        echo "<option value=\"$y\" $selected>$y</option>";
                                    }
                                    ?>
                                </select>
                            </form>
                        </div>
                        <div class="chart-card-body">
                            <?php if (empty($chartSections)): ?>
                                <div class="no-data-message">
                                    <i class="fas fa-chart-bar"></i>
                                    <p>Belum ada data consumable untuk tahun <?= htmlspecialchars($year) ?></p>
                                </div>
                            <?php else: ?>
                                <div class="chart-container">
                                    <canvas id="consumBarChart"></canvas>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="chart-footer">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Batang menunjukkan total qty per section per bulan (status <code>SELESAI</code>).
                                Garis putus-putus menunjukkan akumulasi total qty dari semua section sepanjang tahun.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scripts -->
            <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
            <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
            <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
            <script src="<?= $basePath ?>/assets/vendor/chart.js/Chart.min.js"></script>

            <script>
                console.log('=== DEBUG CHART DATA ===');
                console.log('chartMonths:', <?= json_encode($chartMonths) ?>);
                console.log('chartSections:', <?= json_encode($chartSections) ?>);
                console.log('chartDatasets:', <?= json_encode($chartDatasets) ?>);
                console.log('cumulative:', <?= json_encode($cumulative) ?>);

                /* === Work Order Chart === */
                <?php
                $monthNames = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
                $labelsWo = array_map(fn($d) => $monthNames[$d['month']], $qtyData);
                $totalIn = array_map(fn($d) => $d['total_in'], $qtyData);
                $totalCompleted = array_map(fn($d) => $d['total_completed'], $qtyData);
                ?>

                const labelsWo = <?= json_encode($labelsWo) ?>;
                const totalIn = <?= json_encode($totalIn) ?>;
                const totalCompleted = <?= json_encode($totalCompleted) ?>;

                new Chart(document.getElementById("qtyBarChart"), {
                    type: 'bar',
                    data: {
                        labels: labelsWo,
                        datasets: [{
                                label: "Total WO Masuk",
                                data: totalIn,
                                backgroundColor: "rgba(68, 62, 255, 0.7)",
                                borderColor: "rgba(38, 1, 205, 1)",
                                borderWidth: 1,
                                borderRadius: 6
                            },
                            {
                                label: "WO Completed",
                                data: totalCompleted,
                                backgroundColor: "rgba(86, 221, 86, 0.7)",
                                borderColor: "rgba(95, 192, 75, 1)",
                                borderWidth: 1,
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                /* === Consumable Chart (stacked + cumulative line) === */
                const labelsConsum = <?= json_encode($chartMonths) ?>;
                const sections = <?= json_encode($chartSections) ?>;
                const datasetsPerSection = <?= json_encode($chartDatasets) ?>;
                const cumulativeTotals = <?= json_encode($cumulative) ?>;

                // Hanya render chart jika ada data
                if (sections.length > 0) {
                    const sectionColors = {
                        "Presub": "rgba(255, 193, 7, 0.7)",
                        "K-Line 3": "rgba(54, 162, 235, 0.7)",
                        "K-Line 4 SPS": "rgba(75, 192, 192, 0.7)",
                        "K-Line 5": "rgba(153, 102, 255, 0.7)",
                        "Delivery": "rgba(255, 99, 132, 0.7)"
                    };

                    const barDatasets = sections.map(sec => ({
                        type: 'bar',
                        label: sec,
                        data: datasetsPerSection[sec] || [],
                        backgroundColor: sectionColors[sec] || "rgba(100,100,100,0.7)",
                        stack: 'stack1'
                    }));

                    const lineDataset = {
                        type: 'line',
                        label: 'Total Kumulatif',
                        data: cumulativeTotals,
                        borderColor: 'rgba(0,0,0,0.9)',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 3,
                        borderDash: [6, 6]
                    };

                    new Chart(document.getElementById("consumBarChart"), {
                        type: 'bar',
                        data: {
                            labels: labelsConsum,
                            datasets: [...barDatasets, lineDataset]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        footer(items) {
                                            const total = items
                                                .filter(i => i.dataset.type === 'bar')
                                                .reduce((sum, i) => sum + i.parsed.y, 0);
                                            return 'Total bulan ini: ' + total;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.warn('Tidak ada data consumable untuk ditampilkan');
                }
            </script>
        </div>
    </div>
</body>

</html>