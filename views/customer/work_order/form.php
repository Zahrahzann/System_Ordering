<?php
// Memuat semua class dan memulai session
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Middleware\SessionMiddleware;

SessionMiddleware::requireCustomerLogin();

$isEditMode = isset($item);
$old = $_SESSION['old_input'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEditMode ? 'Edit' : 'Form' ?> Pengajuan Work Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/system_ordering/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/customer/work_order/form.css" rel="stylesheet">
    <link href="/system_ordering/public/assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/../../../views/layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include __DIR__ . '/../../../views/layout/topbar.php'; ?>

                <div class="container-fluid">
                    <h1 class="page-title">
                        <i class="fas fa-<?= $isEditMode ? 'edit' : 'file-alt' ?>"></i>
                        <?= $isEditMode ? 'Edit Item' : 'Form Pengajuan' ?> Work Order
                    </h1>

                    <?php
                    // Menampilkan pesan error atau sukses
                    if (isset($_SESSION['errors'])) {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>';
                        foreach ($_SESSION['errors'] as $error) echo htmlspecialchars($error) . '<br>';
                        echo '</div>';
                        unset($_SESSION['errors']);
                    }
                    ?>

                    <form action="<?= $isEditMode ? "/system_ordering/public/customer/cart/update/{$item['id']}" : "/system_ordering/public/customer/work_order/process_add_to_cart" ?>" method="POST" enctype="multipart/form-data">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <!-- Informasi Barang -->
                                <div class="form-section">
                                    <div class="section-title">
                                        <i class="fas fa-box"></i>
                                        Informasi Barang
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="item_name">Nama Barang <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="item_name" name="item_name" required
                                                    value="<?= htmlspecialchars($old['item_name'] ?? $item['item_name'] ?? '') ?>"
                                                    placeholder="Masukkan nama barang">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Kategori <span class="required">*</span></label>
                                                <select class="form-control" id="category" name="category" required>
                                                    <option value="">-- Pilih Kategori --</option>
                                                    <?php $categories = ['Sparepart', 'Improvement', 'Project', 'Regular']; ?>
                                                    <?php foreach ($categories as $cat) : ?>
                                                        <option value="<?= $cat ?>" <?= ($old['category'] ?? $item['category'] ?? '') == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="quantity">Jumlah (Quantity) <span class="required">*</span></label>
                                                <input type="number" class="form-control" id="quantity" name="quantity" required min="1"
                                                    value="<?= htmlspecialchars($old['quantity'] ?? $item['quantity'] ?? '1') ?>"
                                                    placeholder="1">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="material">Material <span class="required">*</span></label>
                                                <select class="form-control" id="material" name="material" required>
                                                    <option value="">-- Pilih Material --</option>
                                                    <option value="ordered" <?= ($old['material'] ?? $item['material'] ?? '') == 'ordered' ? 'selected' : '' ?>>Ordered</option>
                                                    <option value="ready" <?= ($old['material'] ?? $item['material'] ?? '') == 'ready' ? 'selected' : '' ?>>Ready</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="material_type">Jenis Material <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="material_type" name="material_type" required
                                                    value="<?= htmlspecialchars($old['material_type'] ?? $item['material_type'] ?? '') ?>"
                                                    placeholder="Contoh: Stainless Steel">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="note">Catatan Tambahan</label>
                                        <textarea class="form-control" id="note" name="note" rows="3"
                                            placeholder="Tambahkan catatan atau spesifikasi khusus (opsional)"><?= htmlspecialchars($old['note'] ?? $item['note'] ?? '') ?></textarea>
                                        <small class="form-text">Tuliskan informasi tambahan yang diperlukan</small>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div class="form-section">
                                    <div class="section-title">
                                        <i class="fas fa-file-upload"></i>
                                        File Drawing
                                    </div>

                                    <?php if ($isEditMode && !empty($item['file_path'])): ?>
                                        <div class="current-files">
                                            <div class="current-files-title">File Saat Ini:</div>
                                            <?php
                                            $files = json_decode($item['file_path'], true);
                                            if (is_array($files)) {
                                                foreach ($files as $file) {
                                                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                    $icon = 'fa-file-alt';
                                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                        $icon = 'fa-file-image';
                                                    } elseif ($extension === 'pdf') {
                                                        $icon = 'fa-file-pdf';
                                                    }
                                                    echo '<div class="file-item">';
                                                    echo '<i class="fas ' . $icon . '"></i>';
                                                    echo '<a href="' . htmlspecialchars($file) . '" target="_blank">' . basename($file) . '</a>';
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="file-input-wrapper">
                                        <label for="file_path" class="file-input-display">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p><strong>Klik untuk upload file</strong></p>
                                            <p class="file-info">Format: PDF, JPG, PNG (Max 10MB per file)</p>
                                        </label>
                                        <input type="file" class="form-control-file" id="file_path" name="file_path[]" multiple style="display:none;">
                                    </div>
                                    <small class="form-text">Anda bisa memilih lebih dari satu file</small>
                                </div>

                                <!-- Jadwal & Prioritas -->
                                <div class="form-section">
                                    <div class="section-title">
                                        <i class="fas fa-calendar-alt"></i>
                                        Jadwal & Prioritas
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="needed_date">Tanggal Dibutuhkan <span class="required">*</span></label>
                                                <?php $dateValue = $old['needed_date'] ?? ($isEditMode ? date('Y-m-d\TH:i', strtotime($item['needed_date'])) : ''); ?>
                                                <input type="datetime-local" class="form-control" id="needed_date" name="needed_date" required value="<?= $dateValue ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Jenis Kebutuhan</label>
                                            <div class="emergency-wrapper">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_emergency" name="is_emergency" value="1"
                                                        <?= (isset($old['is_emergency']) || ($item['is_emergency'] ?? false)) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="is_emergency">
                                                        ❗Emergency
                                                    </label>
                                                </div>
                                                <select class="form-control" id="emergency_type" name="emergency_type">
                                                    <option value="">-- Pilih Jenis Emergency --</option>
                                                    <option value="Safety" <?= ($old['emergency_type'] ?? $item['emergency_type'] ?? '') == 'Safety' ? 'selected' : '' ?>>Safety</option>
                                                    <option value="line_stop" <?= ($old['emergency_type'] ?? $item['emergency_type'] ?? '') == 'line_stop' ? 'selected' : '' ?>>Line Stop</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div>
                                    <span class="text-muted small"><span class="required">*</span> Wajib diisi</span>
                                </div>
                                <div>
                                    <?php if ($isEditMode): ?>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="action_type" value="cart" class="btn btn-info">
                                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                        <button type="submit" name="action_type" value="order" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Order Sekarang
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include __DIR__ . '/../../../views/layout/footer.php'; ?>
        </div>
    </div>

    <script src="/system_ordering/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/system_ordering/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/system_ordering/public/assets/js/sb-admin-2.min.js"></script>
    <script>
        // File input display
        document.getElementById('file_path').addEventListener('change', function(e) {
            const display = document.querySelector('.file-input-display');
            const files = e.target.files;

            if (files.length > 0) {
                let fileNames = Array.from(files).map(f => f.name).join(', ');
                display.innerHTML = `
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    <p><strong>${files.length} file terpilih</strong></p>
                    <p class="file-info">${fileNames}</p>
                `;
            }
        });

        // Emergency type toggle
        document.getElementById('is_emergency').addEventListener('change', function() {
            const emergencyType = document.getElementById('emergency_type');
            if (this.checked) {
                emergencyType.required = true;
            } else {
                emergencyType.required = false;
                emergencyType.value = '';
            }
        });
    </script>
    <?php unset($_SESSION['old_input']); ?>
</body>

</html>