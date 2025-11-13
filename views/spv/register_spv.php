<?php
// Data $departments dan $plants akan dikirim oleh AuthController
if (!isset($departments) || !isset($plants)) {
    die('Controller tidak menyediakan data dropdown.');
}
$basePath = '/system_ordering/public';
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SPV Register</title>

    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="<?= $basePath ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
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
                                <h1 class="h4 text-gray-900 mb-4">Create an SPV Account!</h1>
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

                            <form class="user" action="<?= $basePath ?>/spv/process_register" method="POST">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="name" placeholder="Full Name" required value="<?= htmlspecialchars($_SESSION['old_input']['name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="npk" placeholder="NPK" required value="<?= htmlspecialchars($_SESSION['old_input']['npk'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="tel" class="form-control form-control-user" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($_SESSION['old_input']['phone'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($_SESSION['old_input']['email'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <select name="department_id" class="form-control" required style="font-size: .8rem; border-radius: 10rem; height: 50px; padding-left: 1rem;">
                                        <option value="">-- Select Department --</option>
                                        <?php 
                                        $oldDepartmentId = $_SESSION['old_input']['department_id'] ?? '';
                                        foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>" <?= ($oldDepartmentId == $dept['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="plant_id" class="form-control" required style="font-size: .8rem; border-radius: 10rem; height: 50px; padding-left: 1rem;">
                                        <option value="">-- Select Plant --</option>
                                        <?php 
                                        $oldPlantId = $_SESSION['old_input']['plant_id'] ?? '';
                                        foreach ($plants as $plant): ?>
                                            <option value="<?= $plant['id'] ?>" <?= ($oldPlantId == $plant['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($plant['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                                <?php unset($_SESSION['old_input']); // Membersihkan data input lama setelah ditampilkan ?>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?= $basePath ?>/spv/login">Already have an account? Login!</a>
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
</body>
</html>