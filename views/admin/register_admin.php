<?php
$basePath = '/system_ordering/public';
if (session_status() === PHP_SESSION_NONE) session_start();

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Register</title>
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Buat Akun Admin Baru!</h1>
                            </div>

                            <?php if (isset($_SESSION['errors'])): ?>
                                <div class="alert alert-danger small">
                                    <ul class="mb-0">
                                        <?php foreach ($_SESSION['errors'] as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php unset($_SESSION['errors']); ?>
                            <?php endif; ?>

                            <form class="user" action="<?= $basePath ?>/admin/process_register" method="POST">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="name" placeholder="Nama Lengkap" required value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="npk" placeholder="NPK" required value="<?= htmlspecialchars($old['npk'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="tel" class="form-control form-control-user" name="phone" placeholder="Nomor Telepon" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email" placeholder="Alamat Email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">Register Akun</button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?= $basePath ?>/admin/login">Sudah punya akun? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <?php
    if (isset($_GET['status']) && $_GET['status'] === 'reg_success') {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi Berhasil',
                    text: 'Akun Anda telah berhasil dibuat. Silakan login.',
                    confirmButtonColor: '#4e73df'
                });
            });
        </script>";
    }
    ?>
</body>

</html>