<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use Respect\Validation\Validator as v;

class CustomerAuthController
{
    // ==========================================================
    // FUNGSI LOGIN & AUTO-REGISTER CUSTOMER
    // ==========================================================
    public static function loginCustomer(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $input = [
            'name'          => trim($_POST['name'] ?? ''),
            'npk'           => trim($_POST['npk'] ?? ''),
            'phone'         => trim($_POST['phone'] ?? ''),
            'plant_id'      => trim($_POST['plant_id'] ?? ''),
            'department_id' => trim($_POST['department_id'] ?? ''),
            'line'          => trim($_POST['line'] ?? '')
        ];

        $errors = self::validateCustomerData($input);
        if (!empty($errors)) {
            // KOREKSI 1: Redirect error harus ke PUBLIC
            self::handleErrors($errors, '/system_ordering/public/customer/login');
            return;
        }

        $existingCustomer = CustomerModel::findByNpk($input['npk']);

        if ($existingCustomer) {
            if (trim(strtolower($existingCustomer['name'])) === trim(strtolower($input['name']))) {
                $customer = $existingCustomer;
            } else {
                $_SESSION['flash_notification'] = [
                    'type'    => 'error',
                    'title'   => 'Login Gagal',
                    'message' => "NPK '" . htmlspecialchars($input['npk']) . "' sudah terdaftar atas nama pengguna lain."
                ];
                $_SESSION['form_input'] = $input;
                // KOREKSI 2: Redirect error NPK bentrok harus ke PUBLIC
                header("Location: /system_ordering/public/customer/login");
                exit;
            }
        } else {
            try {
                $id = CustomerModel::create($input);
                $customer = CustomerModel::findById($id);
                if (!$customer) {
                    // KOREKSI 3: Redirect error gagal ambil data harus ke PUBLIC
                    self::handleErrors(["Gagal membuat atau mengambil data customer baru."], '/system_ordering/public/customer/login');
                    return;
                }
            } catch (\PDOException $e) {
                // KOREKSI 4: Redirect error PDO harus ke PUBLIC
                self::handleErrors(["Terjadi kesalahan saat registrasi: " . $e->getMessage()], '/system_ordering/public/customer/login');
                return;
            }
        }

        // --- Bagian Sukses (Login atau Register) ---
        $_SESSION['user_data'] = [
            'id' => $customer['id'],
            'name' => $customer['name'],
            'npk' => $customer['npk'],
            'phone' => $customer['phone'],
            'plant_id' => $customer['plant_id'],
            'department_id' => $customer['department_id'],
            'line' => $customer['line'],
            'role' => 'customer'
        ];
        unset($_SESSION['form_input']);
        // KOREKSI 5: Redirect sukses harus ke PUBLIC
        header("Location: /system_ordering/public/customer/dashboard");
        exit;
    }

    // ==========================================================
    // FUNGSI LOGOUT
    // ==========================================================
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        // KOREKSI 6: Redirect logout harus ke PUBLIC
        header("Location: /system_ordering/public/customer/login");
        exit;
    }

    private static function validateCustomerData(array $data): array
    {
        $errors = [];
        if (!v::stringType()->length(3, 100)->validate($data['name'])) $errors[] = "Nama harus 3–100 karakter.";
        if (!v::alnum()->noWhitespace()->validate($data['npk'])) $errors[] = "NPK harus angka/huruf tanpa spasi.";
        if (empty($data['plant_id']) || !v::intVal()->validate($data['plant_id'])) $errors[] = "Plant harus dipilih.";
        if (empty($data['department_id']) || !v::intVal()->validate($data['department_id'])) $errors[] = "Departemen harus dipilih.";
        if (empty($data['line']) || !v::stringType()->length(1, 255)->validate($data['line'])) $errors[] = "Line harus diisi.";
        return $errors;
    }

    // ==========================================================
    // FUNGSI ERROR HANDLER
    // ==========================================================
    private static function handleErrors(array $errors, ?string $redirectPath = null): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['errors'] = $errors;
        // Simpan ke KEDUA session agar kompatibel dengan semua form
        $_SESSION['old_input'] = $_POST;
        $_SESSION['form_input'] = $_POST;
        header("Location: " . $redirectPath);
        exit;
    }
}
