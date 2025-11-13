<?php
// Data $userData sekarang dikirim dari AdminController
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">
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
                        <div class="col-md-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Area Chart</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                    <hr>
                                    Styling for the area chart can be found in the
                                    <code>/system_ordering/public/assets/js/demo/chart-area-demo.js</code> file.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Donut Chart</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <hr>
                                    Styling for the donut chart can be found in the
                                    <code>/system_ordering/public/assets/js/demo/chart-pie-demo.js</code> file.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/../layout/footer.php'; ?>
        </div>
    </div>

    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>

    <!-- Script untuk Chart -->
    <script src="/system_ordering/public/assets/vendor/chart.js/Chart.min.js"></script>
    <script src="/system_ordering/public/assets/js/demo/chart-area-demo.js"></script>
    <script src="/system_ordering/public/assets/js/demo/chart-pie-demo.js"></script>
</body>

</html>