<?php
if (!isset($userData)) die('Controller tidak menyediakan data user.');
if (!isset($qtyData) || !is_array($qtyData)) $qtyData = [];
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
                    </div> <!-- end of container-fluid -->

                    <!-- Chart Card -->
                    <div class="col-12">
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
                    </div>
                </div>
                <?php include __DIR__ . '/../layout/footer.php'; ?>
            </div>
        </div>

        <!-- Scripts -->
        <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
        <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
        <script src="<?= $basePath ?>/assets/vendor/chart.js/Chart.min.js"></script>

        <!-- Bar Chart Script -->
        <?php
        $monthNames = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        $labels = array_map(fn($d) => $monthNames[$d['month']], $qtyData);
        $totalIn = array_map(fn($d) => $d['total_in'], $qtyData);
        $totalCompleted = array_map(fn($d) => $d['total_completed'], $qtyData);
        ?>
        <script>
            const labels = <?= json_encode($labels) ?>;
            const totalIn = <?= json_encode($totalIn) ?>;
            const totalCompleted = <?= json_encode($totalCompleted) ?>;

            new Chart(document.getElementById("qtyBarChart"), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                            label: "Total WO Masuk",
                            data: totalIn,
                            backgroundColor: "rgba(62, 178, 255, 0.7)",
                            borderColor: "rgba(54, 117, 235, 1)",
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: "WO Completed",
                            data: totalCompleted,
                            backgroundColor: "rgba(86, 221, 221, 0.7)",
                            borderColor: "rgba(75, 192, 192, 1)",
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
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    family: 'Poppins',
                                    size: 13
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                family: 'Poppins',
                                size: 13,
                                weight: '600'
                            },
                            bodyFont: {
                                family: 'Poppins',
                                size: 12
                            },
                            cornerRadius: 6
                        }
                    }
                }
            });
        </script>
    </body>

</html>