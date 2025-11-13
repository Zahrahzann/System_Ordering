<?php
if (session_status() == PHP_SESSION_NONE) session_start();

$basePath = '/system_ordering/public';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <!-- Path assets -->
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,400,600,700,800,900" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Muat SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Admin Login</h1>
                            </div>

                            <!-- Form Action -->
                            <form method="POST" action="<?= $basePath ?>/admin/process_login" class="user">
                                <div class="form-group">
                                    <!-- Pengisian email kembali saat terjai error -->
                                    <input type="email" name="email" class="form-control form-control-user" placeholder="Enter Email Address..." required value="<?= htmlspecialchars($_SESSION['old_input']['email'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-user" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <!-- Link register -->
                                <a class="small" href="<?= $basePath ?>/admin/register">Buat Akun Admin Baru!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JS -->
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <!-- Skrip notifikasi SweetAlert -->
    <?php
    $notification = null;
    if (isset($_SESSION['flash_notification'])) { // Notifikasi dari Customer Login
        $notification = $_SESSION['flash_notification'];
        unset($_SESSION['flash_notification']);
    } elseif (isset($_SESSION['errors'])) { // Notifikasi dari handleErrors (Admin/SPV)
        $notification = [
            'type'    => 'error',
            'title'   => 'Login Gagal',
            'message' => implode('<br>', $_SESSION['errors'])
        ];
        unset($_SESSION['errors']);
    }

    if ($notification):
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '" . htmlspecialchars($notification['type']) . "',
                    title: '" . htmlspecialchars($notification['title']) . "',
                    html: '" . addslashes($notification['message']) . "', // Gunakan html agar <br> berfungsi
                    confirmButtonColor: '#4e73df'
                });
            });
        </script>";
    endif;

    // Hapus input lama jika masih ada
    if (isset($_SESSION['old_input'])) {
        unset($_SESSION['old_input']);
    }
    ?>
</body>

</html>