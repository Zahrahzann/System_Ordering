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
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">

        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column w-100" style="min-height: 100vh;">
            <div id="content">

                <?php include __DIR__ . '/../layout/topbar.php'; ?>

                <div class="container-fluid pt-3">
                    <h1 class="h3 mb-3 text-gray-800">Selamat Datang di Dashboard Admin</h1>
                    <p class="lead">Halo, <strong><?= htmlspecialchars($userData['name']) ?></strong></p>

                    <div class="row">
                        <!-- Bar Chart -->
                        <div class="col-12 col-xl-10 mx-auto">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-bar"></i> Report QTY Manufacture Work Order
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mx-auto" style="max-width: 900px;">
                                        <div class="chart-bar" style="height: 300px;">
                                            <canvas id="qtyBarChart"></canvas>
                                        </div>
                                    </div>
                                    <hr>
                                    <small class="text-muted">Data diambil dari Work Order per bulan: total masuk vs yang sudah <code>completed</code>.</small>
                                </div>
                            </div>
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
                        backgroundColor: "rgba(54, 162, 235, 0.7)"
                    },
                    {
                        label: "WO Completed",
                        data: totalCompleted,
                        backgroundColor: "rgba(75, 192, 192, 0.7)"
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
                            stepSize: 1
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
    </script>
</body>

</html>