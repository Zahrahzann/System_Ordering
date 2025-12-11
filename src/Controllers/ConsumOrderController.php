<?php

namespace App\Controllers;

use App\Models\ConsumOrderModel;
use App\Models\ConsumCartModel;
use App\Middleware\SessionMiddleware;
use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

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
        $role = $_SESSION['user_data']['role'] ?? 'customer';

        if ($okOrder && $okDelete) {
            header("Location: /system_ordering/public/{$role}/shared/consumable/orders?status=checkout_success");
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
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if ($role === 'admin') {
            // Admin lihat semua pesanan aktif
            $orders = ConsumOrderModel::getOrdersByRole('admin', null, 'aktif');
        } elseif ($role === 'spv') {
            // SPV lihat pesanan departemen aktif
            $departmentId = $_SESSION['user_data']['department_id'] ?? null;
            $orders = ConsumOrderModel::getOrdersByRole('spv', $departmentId, 'aktif');
        } else {
            // Customer lihat pesanan aktif miliknya
            $orders = ConsumOrderModel::getOrdersByRole('customer', $customerId, 'aktif');
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

        $role = $_SESSION['user_data']['role'] ?? 'customer';
        header("Location: /system_ordering/public/{$role}/consumable/history?status=completed");
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

    public static function reorder()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        $orderId  = $_POST['order_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        $orderCode = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        if (!$orderId || !$customerId) {
            header('Location: /system_ordering/public/customer/shared/consumable/orders?status=reorder_failed');
            exit;
        }

        // Ambil detail order lama + join ke product_items supaya dapat stok
        $oldOrder = ConsumOrderModel::getOrderById($orderId);

        if (!$oldOrder) {
            header('Location: /system_ordering/public/customer/shared/consumable/orders?status=reorder_failed');
            exit;
        }

        // Ambil stok produk
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT stock FROM product_items WHERE id = ?");
        $stmt->execute([$oldOrder['product_item_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $stock  = (int)($product['stock'] ?? 0);
        $status = ($stock >= $quantity) ? 'Ready' : 'Pending';

        // Insert order baru dengan status sesuai stok
        $stmt = $pdo->prepare("
    INSERT INTO consum_orders 
        (order_code, customer_id, product_item_id, product_type_id, section_id, quantity, price, status, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");
        $stmt->execute([
            $orderCode,
            $customerId,
            $oldOrder['product_item_id'],
            $oldOrder['product_type_id'],
            $oldOrder['section_id'],
            $quantity,
            $oldOrder['price'],
            $status
        ]);

        // Kalau status Ready → kurangi stok
        if ($status === 'Ready') {
            $updateStock = $pdo->prepare("UPDATE product_items SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$quantity, $oldOrder['product_item_id']]);
        }

        header('Location: /system_ordering/public/customer/shared/consumable/orders?status=reorder_success');
        exit;
    }
}
