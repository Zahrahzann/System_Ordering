<?php

namespace App\Controllers;

use App\Models\ConsumOrderModel;
use App\Models\ConsumCartModel;
use App\Middleware\SessionMiddleware;

class ConsumOrderController
{
    /** Proses checkout dari cart → buat order */
    public static function processCheckout()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$customerId) {
            header('Location: /system_ordering/public/customer/login');
            exit;
        }

        // Checkbox harus name="selected_items[]" dengan value=cart_id
        $selectedItems = $_POST['selected_items'] ?? [];

        if (empty($selectedItems) || !is_array($selectedItems)) {
            header('Location: /system_ordering/public/customer/consumable/cart?status=no_items_selected');
            exit;
        }

        // Sanitize ids → integer only
        $cartIds = array_map('intval', $selectedItems);

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

    /** Tampilkan daftar order sesuai role */
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

    /** Admin: tandai pesanan sedang dikirim */
    public static function sendOrder($orderId)
    {
        // var_dump("MASUK CONTROLLER", $orderId);
        // exit;
        SessionMiddleware::requireAdminLogin();
        ConsumOrderModel::updateStatus($orderId, 'Dikirim');
        header('Location: /system_ordering/public/admin/shared/consumable/orders?status=shipping');
        exit;
    }

    /** Admin: tandai pesanan selesai */
    public static function completeOrder($orderId)
    {
        SessionMiddleware::requireAdminLogin();
        ConsumOrderModel::updateStatus($orderId, 'Selesai');
        header('Location: /system_ordering/public/admin/shared/consumable/orders?status=complete');
        exit;
    }

    /** Admin: hapus pesanan */
    public static function deleteOrder($orderId)
    {
        SessionMiddleware::requireAdminLogin();
        ConsumOrderModel::delete($orderId);
        header('Location: /system_ordering/public/admin/shared/consumable/orders?status=deleted');
        exit;
    }
}
