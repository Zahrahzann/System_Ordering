<?php
// $users, $title, $departments, $selectedDept dikirim dari Controller
$basePath = '/system_ordering/public';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/admin/manage/spv.css" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../layout/topbar.php'; ?>
                <div class="container-fluid">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-user-tie"></i>
                            <?= htmlspecialchars($title) ?>
                        </h1>
                    </div>

                    <!-- Filter Card -->
                    <div class="filter-card">
                        <form method="GET" action="" class="filter-form">
                            <!-- Search Box - Kiri -->
                            <div class="search-group">
                                <div class="form-group">
                                    <label for="search">Cari Data</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                        placeholder="Nama, NPK..."
                                        value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                                </div>
                            </div>

                            <!-- Filter Department - Kanan -->
                            <div class="filter-group">
                                <div class="form-group">
                                    <label for="department_id">Departemen</label>
                                    <select name="department_id" id="department_id" class="form-control">
                                        <option value="">Semua Departemen</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>" <?= ($selectedDept == $dept['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </form>
                    </div>

                    <div class="stats-card">
                        <div class="stats-info">
                            <h3>Total Supervisor</h3>
                            <p class="count"><?= count($users) ?></p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>

                    <div class="spv-grid">
                        <?php if (empty($users)): ?>
                            <div class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <h4>Tidak Ada Data Supervisor</h4>
                                <p>Tidak ada supervisor yang ditemukan dengan filter yang dipilih.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <div class="spv-card">
                                    <div class="spv-header">
                                        <div class="spv-avatar">
                                            <?= strtoupper(substr($user['name'], 0, 2)) ?>
                                        </div>
                                        <div class="spv-info-header">
                                            <div class="spv-name" title="<?= htmlspecialchars($user['name']) ?>">
                                                <?= htmlspecialchars($user['name']) ?>
                                            </div>
                                            <div class="spv-role">
                                                <i class="fas fa-shield-alt"></i>
                                                Supervisor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="spv-details">
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-id-card"></i></div>
                                            <div class="detail-content">
                                                <div class="detail-label">NPK</div>
                                                <div class="detail-value"><?= htmlspecialchars($user['npk']) ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                                            <div class="detail-content">
                                                <div class="detail-label">Email</div>
                                                <div class="detail-value" title="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-phone"></i></div>
                                            <div class="detail-content">
                                                <div class="detail-label">No. HP</div>
                                                <div class="detail-value"><?= htmlspecialchars($user['phone']) ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-building"></i></div>
                                            <div class="detail-content">
                                                <div class="detail-label">Departemen</div>
                                                <div class="detail-value"><?= htmlspecialchars($user['department_name']) ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-icon"><i class="fas fa-industry"></i></div>
                                            <div class="detail-content">
                                                <div class="detail-label">Plant</div>
                                                <div class="detail-value"><?= htmlspecialchars($user['plant_name']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="spv-actions">
                                        <a href="#" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus supervisor ini?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php include __DIR__ . '/../../layout/footer.php'; ?>
        </div>
    </div>
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>
</body>

</html>