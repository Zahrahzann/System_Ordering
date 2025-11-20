<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\OrderModel;
use App\Middleware\SessionMiddleware;

class CartController
{
    /** Menampilkan halaman keranjang belanja */
    public static function showCart()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'];
        $cartItems = CartModel::getItems($customerId);
        require_once __DIR__ . '/../../views/customer/cart.php';
    }

    /** Menghapus item dari keranjang */
    public static function deleteItem($params)
    {
        SessionMiddleware::requireCustomerLogin();
        $itemId = (int) $params;
        $customerId = $_SESSION['user_data']['id'];
        CartModel::deleteItem($itemId, $customerId);
        header('Location: /system_ordering/public/customer/cart?status=item_deleted');
        exit;
    }

    /** Menampilkan halaman KONFIRMASI checkout */
    public static function showConfirmPage()
    {
        SessionMiddleware::requireCustomerLogin();
        $selectedIds = $_POST['selected_items'] ?? [];
        if (empty($selectedIds)) {
            header('Location: /system_ordering/public/customer/cart');
            exit;
        }
        $cartItems = CartModel::getItemsByIds($_SESSION['user_data']['id'], $selectedIds);
        $_SESSION['checkout_items'] = $selectedIds;
        require_once __DIR__ . '/../../views/customer/work_order/checkout_confirm.php';
    }

    /** Memproses checkout setelah dikonfirmasi */
    public static function processCheckout()
    {
        SessionMiddleware::requireCustomerLogin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        $selectedIds = $_SESSION['checkout_items'] ?? [];
        if (empty($selectedIds)) {
            header('Location: /system_ordering/public/customer/cart');
            exit;
        }

        $result = CartModel::checkout($_SESSION['user_data']['id'], $_SESSION['user_data']['department_id'], $_SESSION['user_data']['plant_id'], $selectedIds);

        unset($_SESSION['checkout_items']);
        $_SESSION['flash_message'] = $result['message'];

        header('Location: /system_ordering/public/customer/checkout');
        exit;
    }

    /** Menampilkan halaman TRACKING approval */
    public static function showTrackingPage()
    {
        SessionMiddleware::requireCustomerLogin();
        $latestOrderItems = OrderModel::getAllItemsForCustomer($_SESSION['user_data']['id']);
        require_once __DIR__ . '/../../views/customer/work_order/process_checkout.php';
    }

    public static function deleteRejectedOrder($params)
    {
        SessionMiddleware::requireCustomerLogin();
        $orderId    = (int) $params;
        $customerId = $_SESSION['user_data']['id'];

        $deleted = OrderModel::deleteRejectedOrder($orderId, $customerId);

        if ($deleted) {
            $_SESSION['flash_notification'] = [
                'type'    => 'success',
                'message' => 'Pesanan yang ditolak berhasil dihapus.'
            ];
        } else {
            $_SESSION['flash_notification'] = [
                'type'    => 'danger',
                'message' => 'Pesanan tidak dapat dihapus (mungkin bukan milik Anda atau status bukan reject).'
            ];
        }

        header('Location: /system_ordering/public/customer/checkout');
        exit;
    }
}
