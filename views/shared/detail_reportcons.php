<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath    = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? null;
$data        = $data ?? [];
$year        = $year ?? date('Y');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Detail Consumable</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
            --light-bg: #f8f9fc;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            padding: 2rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }

        .page-subtitle {
            font-size: 0.95rem;
            opacity: 0.95;
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border: none;
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-card .form-control {
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-card .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .filter-card label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.35rem;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background: #2e59d9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.4);
        }

        .btn-success {
            background: var(--success-color);
            border: none;
        }

        .btn-success:hover {
            background: #17a673;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(28, 200, 138, 0.4);
        }

        /* Section Headers */
        .section-header {
            background: white;
            color: var(--dark-color);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            border-left: 4px solid var(--primary-color);
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: var(--card-shadow);
        }

        .section-header:first-of-type {
            margin-top: 0;
        }

        .type-header {
            background: #f8f9fc;
            color: var(--dark-color);
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            border-left: 3px solid var(--info-color);
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        /* Table Styling */
        .table-responsive {
            border-radius: 0.5rem;
        }

        .table {
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .table thead th {
            background: #f8f9fc;
            color: var(--dark-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            border: 1px solid #e3e6f0;
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            white-space: nowrap;
            text-align: center;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e3e6f0;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.002);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table tbody td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            border-color: #e3e6f0;
            text-align: center;
        }

        .table tbody td:nth-child(2) {
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
        }

        .table tbody td:nth-child(3),
        .table tbody td:nth-child(4),
        .table tbody td:nth-child(18),
        .table tbody td:nth-child(19) {
            font-weight: 600;
        }

        .table tbody td:nth-child(20) {
            background: #e8f5e9;
            color: var(--success-color);
            font-weight: 700;
        }

        /* Month Columns */
        .table tbody td:nth-child(n+5):nth-child(-n+16) {
            background: white;
            font-weight: 500;
        }

        /* Empty State */
        .empty-state {
            background: white;
            padding: 4rem 2rem;
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
            text-align: center;
        }

        .empty-state i {
            color: #d1d3e2;
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #858796;
            margin-bottom: 0;
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
            border-left: 4px solid #e3e6f0;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
            border-left-color: var(--primary-color);
        }

        .summary-card.success {
            border-left-color: #e3e6f0;
        }

        .summary-card.success:hover {
            border-left-color: var(--success-color);
        }

        .summary-card.info {
            border-left-color: #e3e6f0;
        }

        .summary-card.info:hover {
            border-left-color: var(--info-color);
        }

        .summary-card.warning {
            border-left-color: #e3e6f0;
        }

        .summary-card.warning:hover {
            border-left-color: var(--warning-color);
        }

        .summary-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #858796;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .summary-icon {
            font-size: 2rem;
            opacity: 0.08;
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .filter-card .form-inline {
                flex-direction: column;
                align-items: stretch !important;
            }

            .filter-card .form-control,
            .filter-card label,
            .filter-card .btn {
                margin: 0.25rem 0 !important;
                width: 100%;
            }

            .table {
                font-size: 0.75rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.5rem 0.25rem;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-card,
        .filter-card,
        .summary-card {
            animation: fadeInUp 0.5s ease;
        }

        /* Print Styles */
        @media print {

            .filter-card,
            .btn,
            #sidebar,
            #topbar {
                display: none !important;
            }

            .table {
                font-size: 0.7rem;
            }

            .page-header {
                background: #6f42c1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
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
                            <i class="fas fa-boxes mr-2"></i>Laporan Detail Consumable <?= htmlspecialchars($year) ?>
                        </h1>
                        <p class="page-subtitle">
                            <i class="fas fa-info-circle mr-1"></i>Breakdown qty & biaya per section, product type, dan item
                        </p>
                    </div>

                    <!-- Filter & Export -->
                    <div class="filter-card">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <form method="GET" class="form-inline">
                                    <label for="year" class="mr-2">
                                        <i class="fas fa-calendar-alt mr-1"></i>Tahun:
                                    </label>
                                    <select name="year" id="year" class="form-control mr-2">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                                            $selected = ($y == $year) ? 'selected' : '';
                                            echo "<option value=\"$y\" $selected>$y</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter mr-1"></i> Filter Data
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?= $basePath ?>/admin/detail-consumable/export?year=<?= $year ?>" class="btn btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($data)): ?>
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="fas fa-chart-bar fa-5x"></i>
                            <h4>Belum Ada Data Consumable</h4>
                            <p>Tidak ada data consumable untuk tahun <?= htmlspecialchars($year) ?>.</p>
                            <p class="text-muted mt-2">Silakan pilih tahun lain atau tunggu hingga data tersedia.</p>
                        </div>
                    <?php else: ?>
                        <!-- Tabel Detail -->
                        <?php
                        $currentSection = '';
                        $currentType    = '';
                        $no             = 1;

                        foreach ($data as $row):
                            // New Section
                            if ($row['section_name'] !== $currentSection):
                                // Close previous table if exists
                                if ($currentSection !== '') {
                                    echo "</tbody></table></div></div>";
                                }

                                $currentSection = $row['section_name'];
                                echo "<h3 class='section-header'><i class='fas fa-layer-group mr-2'></i>Section: " . htmlspecialchars($currentSection) . "</h3>";
                                $currentType = ''; // Reset type for new section
                            endif;

                            // New Product Type
                            if ($row['product_type'] !== $currentType):
                                // Close previous table if exists (but not section)
                                if ($currentType !== '') {
                                    echo "</tbody></table></div></div>";
                                }

                                $currentType = $row['product_type'];
                                echo "<h4 class='type-header'><i class='fas fa-tag mr-2'></i>Product Type: " . htmlspecialchars($currentType) . "</h4>";
                                echo "<div class='table-card'>
                                    <div class='table-responsive'>
                                        <table class='table table-hover'>
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>PAD NAME</th>
                                                    <th>HARGA<br>INHOUSE (ME)</th>
                                                    <th>HARGA<br>MAKER (VENDOR)</th>
                                                    <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th>
                                                    <th>May</th><th>Jun</th><th>Jul</th><th>Aug</th>
                                                    <th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th>
                                                    <th>TOTAL<br>PCS</th>
                                                    <th>TOTAL<br>HARGA ME</th>
                                                    <th>TOTAL<br>HARGA MAKER</th>
                                                    <th>REDUCE<br>COST</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                $no = 1; // Reset numbering for each type
                            endif;

                            // Table Row
                            echo "<tr>
                                <td>{$no}</td>
                                <td>" . htmlspecialchars($row['item_name']) . "</td>
                                <td>Rp " . number_format($row['inhouse_price'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($row['maker_price'], 0, ',', '.') . "</td>";

                            // Monthly quantities
                            for ($m = 1; $m <= 12; $m++) {
                                $qty = ($row['month'] == $m) ? $row['total_qty'] : 0;
                                echo "<td>" . ($qty > 0 ? number_format($qty) : '-') . "</td>";
                            }

                            echo "<td><strong>" . number_format($row['total_qty']) . "</strong></td>
                                <td>Rp " . number_format($row['total_me'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($row['total_maker'], 0, ',', '.') . "</td>
                                <td><strong>Rp " . number_format($row['total_benefit'], 0, ',', '.') . "</strong></td>
                            </tr>";

                            $no++;
                        endforeach;

                        // Close last table
                        if (!empty($data)) {
                            echo "</tbody></table></div></div>";
                        }
                        ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Vendor JS -->
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

</body>

</html>