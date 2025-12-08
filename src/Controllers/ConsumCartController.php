<?php

namespace App\Controllers;

use App\Models\ConsumCartModel;
use App\Middleware\SessionMiddleware;

class ConsumCartController
{
    /** Menampilkan halaman keranjang consumable */
    public static function showCart()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$customerId) {
            header('Location: /system_ordering/public/customer/login');
            exit;
        }

        $items = ConsumCartModel::getItems($customerId);

        // Ringkasan keranjang
        $selectedItems = $_POST['selected_items'] ?? [];
        $summary = [
            'jumlah_produk_dipilih' => is_array($selectedItems) ? count($selectedItems) : 0,
            'total_qty'             => array_sum(array_map(fn($i) => (int)$i['quantity'], $items)),
            'subtotal'              => array_sum(array_map(fn($i) => (float)$i['price'] * (int)$i['quantity'], $items)),
        ];

        require_once __DIR__ . '/../../views/customer/consumable/consum_cart.php';
    }

    /** Tambah item ke cart (dari katalog) */
    public static function processAdd()
    {
        // SessionMiddleware::requireCustomerLogin();
        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";
        // exit;

        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$customerId) {
            header('Location: /system_ordering/public/customer/login');
            exit;
        }

        $itemId = $_POST['product_item_id'] ?? null;
        $qty    = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $qty    = max(1, $qty);

        if (!$itemId) {
            header('Location: /system_ordering/public/customer/consumable/cart?status=missing_item');
            exit;
        }

        $ok = ConsumCartModel::addItem($customerId, (int)$itemId, $qty);
        header('Location: /system_ordering/public/customer/consumable/cart?status=' . ($ok ? 'item_added' : 'add_failed'));
        exit;
    }

    /** Update qty item di cart */
    public static function processUpdate()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$customerId) {
            header('Location: /system_ordering/public/customer/login');
            exit;
        }

        // Form/JS harus kirim name="id" (cart id) dan name="quantity"
        $cartId = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $qty    = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $qty    = max(1, $qty);

        if (!$cartId) {
            header('Location: /system_ordering/public/customer/consumable/cart?status=missing_cart_id');
            exit;
        }

        ConsumCartModel::updateItem($cartId, $customerId, $qty);
        header('Location: /system_ordering/public/customer/consumable/cart?status=item_updated');
        exit;
    }

    /** Hapus item dari cart */
    public static function processDelete()
    {
        SessionMiddleware::requireCustomerLogin();
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$customerId) {
            header('Location: /system_ordering/public/customer/login');
            exit;
        }

        // Link/button kirimkan ?id=cart_id
        $cartId = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$cartId) {
            header('Location: /system_ordering/public/customer/consumable/cart?status=missing_cart_id');
            exit;
        }

        ConsumCartModel::deleteItem($cartId, $customerId);
        header('Location: /system_ordering/public/customer/consumable/cart?status=item_deleted');
        exit;
    }
}
