<?php
if (!isset($userData)) die('Controller tidak menyediakan data user.');
if (!isset($qtyData) || !is_array($qtyData)) $qtyData = array_fill(1, 12, 0); // fallback kosong
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
                        <!-- Area Chart -->
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-area"></i> QTY WO Completed per Bulan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="qtyAreaChart"></canvas>
                                    </div>
                                    <hr>
                                    Data diambil dari item Work Order yang sudah <code>completed</code> berdasarkan bulan penyelesaian.
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

    <!-- Area Chart Script -->
    <?php
    $monthNames = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
    $labels = array_values($monthNames);
    $values = array_values($qtyData); // langsung ambil dari HistoryModel
    ?>
    <script>
        const qtyLabels = <?= json_encode($labels) ?>;
        const qtyValues = <?= json_encode($values) ?>;

        const ctxQty = document.getElementById("qtyAreaChart").getContext("2d");
        new Chart(ctxQty, {
            type: 'line',
            data: {
                labels: qtyLabels,
                datasets: [{
                    label: "WO Completed",
                    data: qtyValues,
                    backgroundColor: "rgba(78, 115, 223, 0.1)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>