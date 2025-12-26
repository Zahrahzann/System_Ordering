<?php

namespace App\Controllers;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;
use Respect\Validation\Validator as v;

class AuthController
{
    // ==========================================================
    // FUNGSI UNTUK MENAMPILKAN HALAMAN REGISTRASI (ADMIN/SPV)
    // ==========================================================
    public static function showRegisterPage(string $role)
    {
        $pdo = Database::connect();
        $departments = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $plants = $pdo->query("SELECT * FROM plants ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        if ($role === 'admin') {
            require_once __DIR__ . '/../../views/admin/register_admin.php';
        } else {
            require_once __DIR__ . '/../../views/spv/register_spv.php';
        }
    }

    // ==========================================================
    // FUNGSI REGISTRASI (ADMIN/SPV)
    // ========================================================== 
    public static function registerUser(string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $input = [
            'name'          => $_POST['name'] ?? '',
            'npk'           => $_POST['npk'] ?? '',
            'phone'         => $_POST['phone'] ?? '',
            'email'         => $_POST['email'] ?? '',
            'password'      => $_POST['password'] ?? '',
            'plant_id'      => $_POST['plant_id'] ?? null,
            'department_id' => $_POST['department_id'] ?? null,
        ];

        if ($role === 'admin') {
            $errors = self::validateAdminData($input);
            $input['plant_id'] = null;
            $input['department_id'] = null;
        } else {
            $errors = self::validateUserData($input);
        }

        if (!empty($errors)) {
            self::handleErrors($errors, "/system_ordering/public/{$role}/register");
            return;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR npk = ?");
        $stmt->execute([$input['email'], $input['npk']]);
        if ($stmt->fetch()) {
            self::handleErrors(["Email atau NPK sudah terdaftar."], "/system_ordering/public/{$role}/register");
            return;
        }

        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, npk, phone, email, password, role, plant_id, department_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $input['name'],
            $input['npk'],
            $input['phone'],
            $input['email'],
            $hashedPassword,
            $role,
            $input['plant_id'],
            $input['department_id']
        ]);

        header("Location: /system_ordering/public/{$role}/login?status=reg_success");
        exit;
    }

    // ==========================================================
    // FUNGSI LOGIN (ADMIN/SPV)
    // ==========================================================
    public static function loginUser(string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $pdo = Database::connect();

        $sql = "SELECT 
            u.*, 
            d.name AS department_name, 
            p.name AS plant_name
        FROM users u
        LEFT JOIN departments d ON u.department_id = d.id
        LEFT JOIN plants p ON u.plant_id = p.id
        WHERE u.email = ? AND u.role = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_data'] = [
                'id'            => $user['id'],
                'name'          => $user['name'],
                'phone'         => $user['phone'],
                'role'          => $user['role'],
                'plant_id'      => $user['plant_id'],
                'department_id' => $user['department_id'],
                'department'    => $user['department_name'],
                'plant'         => $user['plant_name']
            ];

            // Tambahkan notifikasi sukses
            $_SESSION['flash_notification'] = [
                'type' => 'success',
                'title' => 'Login Berhasil',
                'message' => 'Selamat datang, ' . $user['name'] . '!'
            ];

            if ($role === 'spv') {
                header("Location: /system_ordering/public/spv/dashboard");
            } else {
                header("Location: /system_ordering/public/admin/dashboard");
            }
            exit;
        } else {
            // Notifikasi Login Gagal
            $_SESSION['login_error'] = 'Email atau Password salah!';
            header("Location: /system_ordering/public/{$role}/login");
            exit;
        }
    }

    // ==========================================================
    // FUNGSI LOGOUT (KHUSUS ADMIN & SPV)
    // ==========================================================
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Cek dulu role sebelum session dihancurkan
        $role = $_SESSION['user_data']['role'] ?? 'spv'; // Default ke spv

        session_unset();
        session_destroy();

        if ($role === 'admin') {
            header("Location: /system_ordering/public/admin/login");
        } else {
            header("Location: /system_ordering/public/spv/login");
        }
        exit;
    }

    // ==========================================================
    // FUNGSI VALIDASI
    // ==========================================================
    private static function validateUserData(array $data): array
    {
        $errors = [];
        if (!v::stringType()->length(3, 100)->validate($data['name'])) $errors[] = "Nama harus 3–100 karakter.";
        if (!v::alnum()->noWhitespace()->validate($data['npk'])) $errors[] = "NPK harus angka/huruf tanpa spasi.";
        if (!v::email()->validate($data['email'])) $errors[] = "Email tidak valid.";
        if (!v::stringType()->length(6, 100)->validate($data['password'])) $errors[] = "Password minimal 6 karakter.";
        if (empty($data['plant_id']) || !v::intVal()->validate($data['plant_id'])) $errors[] = "Plant wajib dipilih.";
        if (empty($data['department_id']) || !v::intVal()->validate($data['department_id'])) $errors[] = "Departemen wajib dipilih.";
        return $errors;
    }

    private static function validateAdminData(array $data): array
    {
        $errors = [];
        if (!v::stringType()->length(3, 100)->validate($data['name'])) $errors[] = "Nama harus 3–100 karakter.";
        if (!v::alnum()->noWhitespace()->validate($data['npk'])) $errors[] = "NPK harus angka/huruf tanpa spasi.";
        if (!v::email()->validate($data['email'])) $errors[] = "Email tidak valid.";
        if (!v::stringType()->length(6, 100)->validate($data['password'])) $errors[] = "Password minimal 6 karakter.";
        return $errors;
    }

    // ==========================================================
    // FUNGSI ERROR HANDLER
    // ==========================================================
    private static function handleErrors(array $errors, ?string $redirectPath = null): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header("Location: " . $redirectPath);
        exit;
    }
}
