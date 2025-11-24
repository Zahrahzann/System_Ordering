<?php
$basePath = '/system_ordering/public';
if (session_status() === PHP_SESSION_NONE) session_start();

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #7ac5ffff 0%, #2746d0ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .register-header {
            text-align: center;
            padding: 35px 30px 25px;
            background: linear-gradient(135deg, #AFDDFF 0%, #2746d0ff 100%);
            color: white;
        }

        .register-header i {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .register-header h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .register-header p {
            font-size: 13px;
            opacity: 0.9;
        }

        .register-body {
            padding: 30px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-danger {
            background: #ffe0e0;
            color: #d63031;
            border-left: 3px solid #d63031;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert li {
            margin-bottom: 3px;
        }

        .alert li:last-child {
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 11px 15px 11px 42px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #60B5FF;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-register {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #7ac5ffff 0%, #2746d0ff 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .divider {
            margin: 22px 0;
            text-align: center;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            position: relative;
            background: white;
            padding: 0 15px;
            color: #888;
            font-size: 13px;
        }

        .login-link {
            text-align: center;
            margin-top: 18px;
        }

        .login-link a {
            color: #7ac5ffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .login-link a:hover {
            color: #2746d0ff;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            body {
                padding: 20px 15px;
            }

            .register-header {
                padding: 25px 20px 20px;
            }

            .register-header h1 {
                font-size: 20px;
            }

            .register-body {
                padding: 25px 20px;
            }

            .form-group {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h1>Create Admin Account</h1>
                <p>Administrator Registration</p>
            </div>

            <div class="register-body">
                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <form class="user" action="<?= $basePath ?>/admin/process_register" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Masukkan nama lengkap"
                                required
                                value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="npk">NPK</label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card"></i>
                            <input
                                type="text"
                                class="form-control"
                                id="npk"
                                name="npk"
                                placeholder="Masukkan NPK"
                                required
                                value="<?= htmlspecialchars($old['npk'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input
                                type="tel"
                                class="form-control"
                                id="phone"
                                name="phone"
                                placeholder="Masukkan nomor telepon"
                                value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Masukkan alamat email"
                                required
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Buat password"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-check"></i>
                        Register Akun
                    </button>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="login-link">
                    <a href="<?= $basePath ?>/admin/login">
                        <i class="fas fa-sign-in-alt"></i> Sudah punya akun? Login!
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= $basePath ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $basePath ?>/assets/js/sb-admin-2.min.js"></script>

    <!-- SweetAlert Success Script -->
    <?php
    if (isset($_GET['status']) && $_GET['status'] === 'reg_success') {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi Berhasil',
                    text: 'Akun Anda telah berhasil dibuat. Silakan login.',
                    confirmButtonColor: '#667eea'
                });
            });
        </script>";
    }
    ?>
</body>

</html>