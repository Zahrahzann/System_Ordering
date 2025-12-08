<?php

namespace App\Controllers;

use App\Models\ConsumOrderModel;
use App\Models\ConsumCartModel;
use App\Middleware\SessionMiddleware;

class ConsumOrderController
{
    /** Proses checkout dari cart → buat order */
    /** Checkout cart (hanya item yang dicentang) */
    public static function processCheckout()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$customerId) {
            header('Location: /system_ordering/public/customer/login');
            exit;
        }

        // Konsisten: checkbox harus name="selected_items[]" dengan value=cart_id
        $selectedItems = $_POST['selected_items'] ?? [];

        if (empty($selectedItems) || !is_array($selectedItems)) {
            header('Location: /system_ordering/public/customer/consumable/cart?status=no_items_selected');
            exit;
        }

        // Sanitize ids → integer only
        $cartIds = array_map('intval', $selectedItems);

        // var_dump($_POST);
        // exit;


        // Buat order dan hapus item yang di-checkout
        $okOrder  = ConsumCartModel::checkoutSelected($customerId, $cartIds);
        $okDelete = $okOrder ? ConsumCartModel::deleteSelected($customerId, $cartIds) : false;

        if ($okOrder && $okDelete) {
            header('Location: /system_ordering/public/shared/consumable/orders?status=checkout_success');
        } else {
            header('Location: /system_ordering/public/customer/consumable/cart?status=checkout_failed');
        }
        exit;
    }

    /** Tampilkan daftar order milik customer */
    public static function showOrders()
    {
        SessionMiddleware::requireLogin();

        $role       = $_SESSION['user_data']['role'] ?? 'customer';
        $customerId = $_SESSION['user_data']['id'];

        if ($role === 'admin') {
            // Admin lihat semua pesanan
            $orders = ConsumOrderModel::getAllOrders();
        } elseif ($role === 'spv') {
            // SPV lihat pesanan departemen
            $departmentId = $_SESSION['user_data']['department_id'] ?? null;
            $orders = ConsumOrderModel::getOrdersByDepartment($departmentId);
        } else {
            // Customer lihat pesanan sendiri
            $orders = ConsumOrderModel::getOrders($customerId);
        }

        require_once __DIR__ . "/../../views/shared/consum-orders.php";
    }
}
