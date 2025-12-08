<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ConsumCartModel
{
    /**
     * Ambil semua item di cart milik customer.
     */
    public static function getItems($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT c.id, c.product_item_id, c.quantity,
                   p.name, p.price, p.product_type_id,
                   pt.name AS product_type,
                   s.name AS section,
                   p.file_path, p.image_path
            FROM consum_cart c
            JOIN product_items p ON c.product_item_id = p.id
            LEFT JOIN product_types pt ON p.product_type_id = pt.id
            LEFT JOIN sections s ON p.section_id = s.id
            WHERE c.customer_id = ?
            ORDER BY c.added_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah item ke cart (qty naik kalau sudah ada).
     */
    public static function addItem($customerId, $productItemId, $qty)
    {
        $qty = max(1, (int)$qty);

        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            INSERT INTO consum_cart (customer_id, product_type_id, product_item_id, section_id, quantity)
            SELECT ?, p.product_type_id, p.id, p.section_id, ?
            FROM product_items p
            WHERE p.id = ?
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ");

        $result = $stmt->execute([$customerId, $qty, $productItemId]);
        if (!$result) {
            echo "Insert gagal: customer=$customerId, item=$productItemId, qty=$qty";
            exit;
        }
        return $result;
    }

    /**
     * Update qty item di cart.
     */
    public static function updateItem($cartId, $customerId, $qty)
    {
        $qty = max(1, (int)$qty);

        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            UPDATE consum_cart 
            SET quantity=? 
            WHERE id=? AND customer_id=?
        ");
        return $stmt->execute([$qty, $cartId, $customerId]);
    }

    /**
     * Hapus item dari cart.
     */
    public static function deleteItem($cartId, $customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            DELETE FROM consum_cart 
            WHERE id=? AND customer_id=?
        ");
        return $stmt->execute([$cartId, $customerId]);
    }

    /**
     * Kosongkan cart (dipakai saat checkout).
     */
    public static function clearCart($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            DELETE FROM consum_cart 
            WHERE customer_id=?
        ");
        return $stmt->execute([$customerId]);
    }

    /**
     * Checkout item tertentu dari cart (berdasarkan array id cart).
     */
    public static function checkoutSelected($customerId, array $cartIds): bool
    {
        if (empty($cartIds)) {
            return false;
        }

        $pdo = Database::connect();
        $pdo->beginTransaction();

        $in  = str_repeat('?,', count($cartIds) - 1) . '?';

        // Ambil semua data yang dibutuhkan untuk insert
        $stmt = $pdo->prepare("
            SELECT c.id, c.product_item_id, c.quantity, 
                   p.name, p.price, p.product_type_id, p.section_id, p.stock
            FROM consum_cart c
            JOIN product_items p ON c.product_item_id = p.id
            WHERE c.customer_id = ? AND c.id IN ($in)
        ");
        $stmt->execute(array_merge([$customerId], $cartIds));
        $selectedItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        try {
            if (empty($selectedItems)) {
                $pdo->rollBack();
                return false;
            }

            foreach ($selectedItems as $item) {
                $orderCode = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
                $qty = max(1, (int)$item['quantity']);

                // Tentukan status berdasarkan stok
                $status = 'Pending';
                if ((int)$item['stock'] >= $qty) {
                    $status = 'Ready';
                    // Kurangi stok
                    $updateStock = $pdo->prepare("UPDATE product_items SET stock = stock - ? WHERE id=?");
                    $updateStock->execute([$qty, $item['product_item_id']]);
                }

                $orderStmt = $pdo->prepare("
                    INSERT INTO consum_orders 
                        (customer_id, product_type_id, product_item_id, section_id, quantity, price, order_code, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $orderStmt->execute([
                    $customerId,
                    $item['product_type_id'],
                    $item['product_item_id'],
                    $item['section_id'],
                    $qty,
                    $item['price'],
                    $orderCode,
                    $status
                ]);
            }

            // Hapus item dari cart setelah sukses insert
            $delStmt = $pdo->prepare("
                DELETE FROM consum_cart 
                WHERE customer_id=? AND id IN ($in)
            ");
            $delStmt->execute(array_merge([$customerId], $cartIds));

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo "<pre>";
            echo "Checkout error: " . $e->getMessage() . "\n";
            echo "Cart IDs: ";
            print_r($cartIds);
            echo "Selected Items: ";
            print_r($selectedItems);
            echo "</pre>";
            exit;
        }
    }

    /**
     * Hapus item tertentu dari cart (berdasarkan array id cart).
     */
    public static function deleteSelected($customerId, array $cartIds): bool
    {
        if (empty($cartIds)) return false;

        $pdo = Database::connect();
        $in  = str_repeat('?,', count($cartIds) - 1) . '?';

        $stmt = $pdo->prepare("
            DELETE FROM consum_cart 
            WHERE customer_id=? AND id IN ($in)
        ");
        return $stmt->execute(array_merge([$customerId], $cartIds));
    }
}
