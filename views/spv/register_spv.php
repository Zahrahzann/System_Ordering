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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SPV</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
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
            box-shadow: 0 0 0 3px rgba(96, 181, 255, 0.1);
        }

        select.form-control {
            padding-left: 42px;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
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
                <h1>Create SPV Account</h1>
                <p>Supervisor Registration</p>
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

                <form class="user" action="<?= $basePath ?>/spv/process_register" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Enter your full name"
                                required
                                value="<?= htmlspecialchars($_SESSION['old_input']['name'] ?? '') ?>">
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
                                placeholder="Enter your NPK"
                                required
                                value="<?= htmlspecialchars($_SESSION['old_input']['npk'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input
                                type="tel"
                                class="form-control"
                                id="phone"
                                name="phone"
                                placeholder="Enter your phone number"
                                value="<?= htmlspecialchars($_SESSION['old_input']['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Enter your email"
                                required
                                value="<?= htmlspecialchars($_SESSION['old_input']['email'] ?? '') ?>">
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
                                placeholder="Create a password"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <div class="input-wrapper">
                            <i class="fas fa-building"></i>
                            <select name="department_id" id="department_id" class="form-control" required>
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
                    </div>

                    <div class="form-group">
                        <label for="plant_id">Plant</label>
                        <div class="input-wrapper">
                            <i class="fas fa-industry"></i>
                            <select name="plant_id" id="plant_id" class="form-control" required>
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
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-check"></i> Register Account
                    </button>

                    <?php unset($_SESSION['old_input']); ?>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="login-link">
                    <a href="<?= $basePath ?>/spv/login">
                        <i class="fas fa-sign-in-alt"></i> Already have an account? Login!
                    </a>
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