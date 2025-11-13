<?php
$basePath = '/system_ordering/public';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SPV</title>
    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center"><h1 class="h4 text-gray-900 mb-4">SPV Login</h1></div>
                            <?php if (isset($_SESSION['login_error'])): ?><div class="alert alert-danger"><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div><?php endif; ?>
                            <?php if (isset($_GET['status']) && $_GET['status'] === 'reg_success'): ?><div class="alert alert-success">Registrasi berhasil! Silakan login.</div><?php endif; ?>
                            
                            <form class="user" action="<?= $basePath ?>/spv/process_login" method="POST">
                                <div class="form-group"><input type="email" class="form-control form-control-user" name="email" placeholder="Enter Email Address..." required></div>
                                <div class="form-group"><input type="password" class="form-control form-control-user" name="password" placeholder="Password" required></div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                            </form>
                            <hr>
                            <div class="text-center"><a class="small" href="<?= $basePath ?>/spv/register">Create an SPV Account!</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>