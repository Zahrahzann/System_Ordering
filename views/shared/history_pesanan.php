<?php
$basePath = '/system_ordering/public';
$currentRole = $_SESSION['user_data']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan Work Order</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid">

                    <h1 class="h3 mb-4 text-gray-800">
                        <?php if ($currentRole === 'admin'): ?>
                            Riwayat Pesanan Work Order (Semua Departemen)
                        <?php elseif ($currentRole === 'spv'): ?>
                            Riwayat Pesanan Departemen Saya
                        <?php else: ?>
                            Riwayat Pesanan Saya
                        <?php endif; ?>
                    </h1>

                    <!-- Filter Tahun & Bulan -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="GET" action="" class="form-inline">
                                <div class="form-group mr-2">
                                    <label for="year" class="mr-2">Tahun:</label>
                                    <select name="year" id="year" class="form-control">
                                        <?php foreach ($availableYears as $y): ?>
                                            <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="month" class="mr-2">Bulan:</label>
                                    <select name="month" id="month" class="form-control">
                                        <option value="">Semua Bulan</option>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Riwayat -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Item Selesai</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID Item</th>
                                            <th>Nama Item</th>
                                            <th>Qty</th>
                                            <th>Kategori</th>
                                            <th>PIC MFG</th>
                                            <th>Tgl Selesai</th>
                                            <?php if ($currentRole === 'customer'): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($items)): ?>
                                            <tr>
                                                <td colspan="<?= $currentRole === 'customer' ? '7' : '6' ?>" class="text-center">
                                                    Tidak ada riwayat untuk periode ini.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['item_id']) ?></td>
                                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                    <td><?= htmlspecialchars($item['category'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($item['pic_mfg'] ?? '-') ?></td>
                                                    <td><?= date('d M Y', strtotime($item['completed_date'])) ?></td>
                                                    <?php if ($currentRole === 'customer'): ?>
                                                        <td>
                                                            <a href="<?= $basePath ?>/customer/history/reorder/<?= $item['item_id'] ?>"
                                                                class="btn btn-success btn-sm"
                                                                onclick="return confirm('Item ini akan ditambahkan kembali ke keranjang Anda. Lanjutkan?')">
                                                                <i class="fas fa-redo"></i> Pesan Lagi
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php include __DIR__ . '/../layout/footer.php'; ?>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>