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

if (session_status() === PHP_SESSION_NONE) session_start();
$notif = null;
if (isset($_SESSION['flash_notification'])) {
    $notif = $_SESSION['flash_notification'];
    unset($_SESSION['flash_notification']);
}

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
    <link href="<?= $basePath ?>/assets/css/admin/dashboard_admin.css?v=<?= time() ?>" rel="stylesheet">
    <script src="<?= $basePath ?>/assets/vendor/chart.js/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

</head>

<body id="page-top">
    <div id="wrapper">

        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column w-100">
            <div id="content">

                <?php include __DIR__ . '/../layout/topbar.php'; ?>

                <div class="container-fluid">
                    <!-- Welcome Card -->
                    <div class="welcome-card">
                        <div class="welcome-content">
                            <div class="welcome-left">
                                <h1 class="welcome-title">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Selamat datang, <?= htmlspecialchars($userData['name'] ?? 'Admin') ?>!
                                </h1>
                                <p class="welcome-subtitle">Kelola sistem ordering dan monitoring work order dengan mudah</p>
                            </div>
                            <div class="welcome-right">
                                <div class="profile-avatar">
                                    <?php if (!empty($userData['photo'])): ?>
                                        <img src="<?= htmlspecialchars($userData['photo']) ?>" alt="Profile Photo">
                                    <?php else: ?>
                                        <i class="fas fa-user-shield"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="role-badge">
                                    <i class="fas fa-crown mr-1"></i> Administrator
                                </div>
                            </div>
                        </div>
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

                        <div class="stat-card blue">
                            <div class="stat-card-content">
                                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                                <div class="stat-details">
                                    <div class="stat-label">WO Completed</div>
                                    <div class="stat-value"><?= number_format($totalWOCompleted) ?></div>
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
                        <div class="chart-card-header">
                            <h2><i class="fas fa-box"></i> ME - Consumable Part <?= htmlspecialchars($year) ?></h2>
                            <!-- Dropdown Tahun -->
                            <form method="get" class="year-dropdown-form">
                                <label for="year">Tahun:</label>
                                <select name="year" id="year" onchange="this.form.submit()">
                                    <?php
                                    $currentYear = date('Y');
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
            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

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
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }],
                            xAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }]
                        },
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                formatter: Math.round,
                                font: {
                                    weight: 'bold'
                                },
                                color: '#000'
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                    plugins: [ChartDataLabels]
                });

                /* === Consumable Chart (grouped bar + cumulative line) === */
                const labelsConsum = <?= json_encode($chartMonths) ?>;
                const sections = <?= json_encode($chartSections) ?>;
                const datasetsPerSection = <?= json_encode($chartDatasets) ?>;
                const cumulativeTotals = <?= json_encode($cumulative) ?>;

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
                        backgroundColor: sectionColors[sec] || "rgba(100,100,100,0.7)"
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
                                xAxes: [{
                                    stacked: false
                                }],
                                yAxes: [{
                                    stacked: false,
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            plugins: {
                                datalabels: {
                                    anchor: 'end',
                                    align: 'top',
                                    formatter: Math.round,
                                    font: {
                                        weight: 'bold'
                                    },
                                    color: '#000'
                                }
                            },
                            legend: {
                                position: 'bottom'
                            },
                            tooltips: {
                                callbacks: {
                                    footer(items) {
                                        const total = items
                                            .filter(i => i.dataset.type === 'bar')
                                            .reduce((sum, i) => sum + i.yLabel, 0);
                                        return 'Total bulan ini: ' + total;
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                } else {
                    console.warn('Tidak ada data consumable untuk ditampilkan');
                }
            </script>

            <!-- SweetAlert Pop-up -->
            <?php if ($notif): ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: '<?= $notif['type'] ?>',
                            title: '<?= $notif['title'] ?>',
                            text: '<?= $notif['message'] ?>',
                            showConfirmButton: true,
                            timer: <?= $notif['type'] === 'success' ? '3000' : 'null' ?>,
                            confirmButtonColor: '#667eea'
                        });
                    });
                </script>
            <?php endif; ?>

        </div>
    </div>
</body>

</html>