<?php

namespace App\Controllers;

use App\Models\ConsumOrderModel;
use App\Models\ConsumCartModel;
use App\Models\ConsumableReportModel;
use App\Models\NotificationModel;
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

        $selectedItems = $_POST['selected_items'] ?? [];
        if (empty($selectedItems) || !is_array($selectedItems)) {
            header('Location: /system_ordering/public/customer/consumable/cart?status=no_items_selected');
            exit;
        }

        $cartIds = array_map('intval', $selectedItems);

        $okOrder  = ConsumCartModel::checkoutSelected($customerId, $cartIds);
        $okDelete = $okOrder ? ConsumCartModel::deleteSelected($customerId, $cartIds) : false;
        $role     = $_SESSION['user_data']['role'] ?? 'customer';

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
            $orders = ConsumOrderModel::getOrdersByRole('admin', null, 'aktif');
        } elseif ($role === 'spv') {
            $departmentId = $_SESSION['user_data']['department_id'] ?? null;
            $orders = ConsumOrderModel::getOrdersByRole('spv', $departmentId, 'aktif');
        } else {
            $orders = ConsumOrderModel::getOrdersByRole('customer', $customerId, 'aktif');
        }

        require_once __DIR__ . "/../../views/shared/consum-orders.php";
    }

    /** Admin: tandai pesanan sedang dikirim */
    public static function sendOrder($orderId)
    {
        SessionMiddleware::requireAdminLogin();
        ConsumOrderModel::updateStatus($orderId, 'Dikirim');

        // Ambil detail order untuk tahu customer_id
        $order = ConsumOrderModel::getOrderById($orderId);
        if ($order) {
            NotificationModel::createWithOrderId(
                $order['customer_id'],                                // targetKey
                "Pesanan Anda #{$order['order_code']} sedang dikirim 🚚", // message
                'fas fa-truck',                                       // icon
                'info',                                               // color
                'shipping',                                           // type
                'customer',                                           // role
                $orderId                                              // orderI
            );
        }

        // Admin tetap dapat popup internal
        header('Location: /system_ordering/public/admin/shared/consumable/orders?status=shipping');
        exit;
    }

    /** Admin: tandai pesanan selesai */
    public static function completeOrder($orderId)
    {
        SessionMiddleware::requireAdminLogin();
        ConsumOrderModel::updateStatus($orderId, 'Selesai');
        $order = ConsumOrderModel::getOrderById($orderId);

        if ($order && !empty($order['items'])) {
            $month = (int)date('n');
            $year = (int)date('Y');
            foreach ($order['items'] as $item) {
                ConsumableReportModel::saveQty(
                    $item['section_id'],
                    $item['product_type_id'],
                    $item['product_item_id'],
                    $month,
                    $year,
                    (int)$item['quantity']
                );
            }

            // Tambahkan notifikasi untuk customer
            NotificationModel::createWithOrderId(
                $order['customer_id'],
                "Pesanan Anda #{$order['order_code']} sudah selesai 🎉",
                'fas fa-check-circle',
                'success',
                'completed',
                'customer',
                $orderId
            );
        }

        // Admin tetap dapat popup internal
        header("Location: /system_ordering/public/admin/consumable/history?status=completed");
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

    /** Order langsung dari katalog consumable */
    public static function orderNow()
    {
        SessionMiddleware::requireCustomerLogin();

        $customerId   = $_SESSION['user_data']['id'] ?? null;
        $departmentId = $_SESSION['user_data']['department_id'] ?? null;
        $plantId      = $_SESSION['user_data']['plant_id'] ?? null;
        $itemId       = $_GET['item'] ?? null;
        $qty          = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

        if (!$customerId || !$itemId || !$departmentId || !$plantId) {
            http_response_code(400);
            echo "Data tidak lengkap.";
            exit;
        }

        // Panggil model untuk buat order langsung
        $result = ConsumOrderModel::orderNow($customerId, $departmentId, $plantId, $itemId, $qty);

        $_SESSION['flash_message'] = $result['message'] ?? 'Order berhasil diproses.';
        header('Location: /system_ordering/public/customer/shared/consumable/orders?status=order_success');
        exit;
    }

    /** Customer: reorder berdasarkan order sebelumnya */
    public static function reorder()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        $orderId        = $_POST['order_id'] ?? null;
        $productItemId  = $_POST['product_item_id'] ?? null;
        $productTypeId  = $_POST['product_type_id'] ?? null;
        $sectionId      = $_POST['section_id'] ?? null;
        $price          = $_POST['price'] ?? null;
        $quantity       = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $orderCode      = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        if (!$orderId || !$customerId || !$productItemId || !$productTypeId || !$sectionId || !$price) {
            header('Location: /system_ordering/public/customer/shared/consumable/orders?status=reorder_failed');
            exit;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT stock FROM product_items WHERE id = ?");
        $stmt->execute([$productItemId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $stock  = (int)($product['stock'] ?? 0);
        $status = ($stock >= $quantity) ? 'Ready' : 'Pending';

        $stmt = $pdo->prepare("
        INSERT INTO consum_orders 
            (order_code, customer_id, product_item_id, product_type_id, section_id, quantity, price, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
        $stmt->execute([
            $orderCode,
            $customerId,
            $productItemId,
            $productTypeId,
            $sectionId,
            $quantity,
            $price,
            $status
        ]);

        if ($status === 'Ready') {
            $updateStock = $pdo->prepare("UPDATE product_items SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$quantity, $productItemId]);
        }

        header('Location: /system_ordering/public/customer/shared/consumable/orders?status=reorder_success');
        exit;
    }
}
