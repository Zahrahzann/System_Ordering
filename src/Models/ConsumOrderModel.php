<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ConsumOrderModel
{
    /**
     * Buat order dari cart dengan status otomatis (Ready/Pending).
     */
    public static function createOrderFromCart($customerId, array $cartIds): bool
    {
        if (empty($cartIds)) return false;

        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            $in = str_repeat('?,', count($cartIds) - 1) . '?';

            // Ambil item dari cart
            $stmt = $pdo->prepare("
                SELECT c.id, c.product_item_id, c.quantity, p.price, p.stock
                FROM consum_cart c
                JOIN product_items p ON c.product_item_id = p.id
                WHERE c.customer_id = ? AND c.id IN ($in)
            ");
            $stmt->execute(array_merge([$customerId], $cartIds));
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $qty   = (int)$item['quantity'];
                $stock = (int)$item['stock'];
                $status = ($stock >= $qty) ? 'Ready' : 'Pending';

                // Simpan ke orders
                $orderStmt = $pdo->prepare("
                    INSERT INTO consum_orders 
                        (customer_id, product_item_id, quantity, price, status, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $orderStmt->execute([
                    $customerId,
                    $item['product_item_id'],
                    $qty,
                    $item['price'],
                    $status
                ]);

                // Kurangi stok jika status Ready
                if ($status === 'Ready') {
                    $updateStock = $pdo->prepare("
                        UPDATE product_items SET stock = stock - ? WHERE id = ?
                    ");
                    $updateStock->execute([$qty, $item['product_item_id']]);
                }
            }

            // Hapus dari cart
            $delStmt = $pdo->prepare("
                DELETE FROM consum_cart WHERE customer_id=? AND id IN ($in)
            ");
            $delStmt->execute(array_merge([$customerId], $cartIds));

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Ambil semua order milik customer.
     */
    public static function getOrders($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
        SELECT o.id, o.product_item_id, o.quantity, o.price, o.status, o.created_at,
                o.order_code,
                p.name AS product_name,
                p.item_code AS item_code,
                p.image_path AS product_image,
                p.file_path AS drawing_file,
                pt.name AS product_type,
                s.name AS section_name,
                c.name AS customer_name,
                c.line,
                d.name AS department
        FROM consum_orders o
        JOIN product_items p ON o.product_item_id = p.id
        JOIN product_types pt ON p.product_type_id = pt.id
        JOIN sections s ON p.section_id = s.id
        JOIN customers c ON o.customer_id = c.id
        JOIN departments d ON c.department_id = d.id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua order (untuk admin).
     */
    public static function getAllOrders()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("
        SELECT o.id, o.product_item_id, o.quantity, o.price, o.status, o.created_at,
                o.order_code,
                p.name AS product_name,
                p.item_code AS item_code,
                p.image_path AS product_image,
                p.file_path AS drawing_file,
                pt.name AS product_type,
                s.name AS section_name,
                c.name AS customer_name,
                c.line,
                d.name AS department
        FROM consum_orders o
        JOIN product_items p ON o.product_item_id = p.id
        JOIN product_types pt ON p.product_type_id = pt.id
        JOIN sections s ON p.section_id = s.id
        JOIN customers c ON o.customer_id = c.id
        JOIN departments d ON c.department_id = d.id
        ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil order berdasarkan departemen (untuk supervisor).
     */
    public static function getOrdersByDepartment($departmentId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
        SELECT o.id, o.product_item_id, o.quantity, o.price, o.status, o.created_at,
                o.order_code,
                p.name AS product_name,
                p.item_code AS item_code,
                p.image_path AS product_image,
                p.file_path AS drawing_file,
                pt.name AS product_type,
                s.name AS section_name,
                c.name AS customer_name,
                c.line,
                d.name AS department
        FROM consum_orders o
        JOIN product_items p ON o.product_item_id = p.id
        JOIN product_types pt ON p.product_type_id = pt.id
        JOIN sections s ON p.section_id = s.id
        JOIN customers c ON o.customer_id = c.id
        JOIN departments d ON c.department_id = d.id
        WHERE c.department_id = ?
        ORDER BY o.created_at DESC
        ");
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update status pesanan (dipakai admin).
     */
    public static function updateStatus($orderId, string $newStatus): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE consum_orders SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$newStatus, $orderId]);
    }
}
