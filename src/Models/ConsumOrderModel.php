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
                        (customer_id, product_item_id, quantity, price, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
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
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil order sesuai role & status.
     * - role = 'customer' → filter customer_id
     * - role = 'spv'      → filter department_id
     * - role = 'admin'    → semua order
     * - status bisa 'Selesai' atau 'aktif' (selain Selesai)
     */
    public static function getOrdersByRole(string $role, ?int $id = null, ?string $status = null): array
    {
        $pdo = Database::connect();

        $sql = "
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
            WHERE 1=1
        ";

        $params = [];

        // filter role
        if ($role === 'customer' && $id) {
            $sql .= " AND o.customer_id = ?";
            $params[] = $id;
        } elseif ($role === 'spv' && $id) {
            $sql .= " AND c.department_id = ?";
            $params[] = $id;
        }
        // admin → tidak ada filter tambahan

        // filter status
        if ($status === 'Selesai') {
            $sql .= " AND o.status = 'Selesai'";
        } elseif ($status === 'aktif') {
            $sql .= " AND o.status != 'Selesai'";
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update status pesanan.
     */
    public static function updateStatus($orderId, string $newStatus): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE consum_orders SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$newStatus, $orderId]);
    }

    public static function markAsShipped($orderId): bool
    {
        return self::updateStatus($orderId, 'Dikirim');
    }

    public static function markAsCompleted($orderId): bool
    {
        return self::updateStatus($orderId, 'Selesai');
    }

    public static function delete($orderId): bool
    {
        $pdo = Database::connect();

        // Ambil detail order dulu
        $stmt = $pdo->prepare("SELECT product_item_id, quantity, status FROM consum_orders WHERE id=?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Kalo status Ready atau Pending → balikin stok
            if (in_array($order['status'], ['Ready', 'Pending'])) {
                $updateStock = $pdo->prepare("UPDATE product_items SET stock = stock + ? WHERE id = ?");
                $updateStock->execute([$order['quantity'], $order['product_item_id']]);
            }

            // Hapus order
            $stmt = $pdo->prepare("DELETE FROM consum_orders WHERE id = ?");
            return $stmt->execute([$orderId]);
        }

        return false;
    }

    public static function getOrderById($orderId): array
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
        SELECT o.id, o.order_code, o.customer_id, o.status, o.created_at, o.updated_at,
               o.product_item_id, o.product_type_id, o.section_id, o.quantity, o.price
        FROM consum_orders o
        WHERE o.id = ?
    ");
        $stmt->execute([$orderId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) return [];

        $order = [
            'id'         => $rows[0]['id'],
            'order_code' => $rows[0]['order_code'],
            'customer_id' => $rows[0]['customer_id'],
            'status'     => $rows[0]['status'],
            'created_at' => $rows[0]['created_at'],
            'updated_at' => $rows[0]['updated_at'],
            'items'      => []
        ];

        foreach ($rows as $r) {
            $order['items'][] = [
                'section_id'      => $r['section_id'],
                'product_type_id' => $r['product_type_id'],
                'product_item_id' => $r['product_item_id'],
                'quantity'        => $r['quantity'],
                'price'           => $r['price']
            ];
        }

        return $order;
    }

    // Cek dan update order pending jika stok sudah cukup
    public static function checkAndUpdatePendingOrders($productItemId)
    {
        $pdo = Database::connect();

        // Ambil stok terbaru
        $stmt = $pdo->prepare("SELECT stock FROM product_items WHERE id = ?");
        $stmt->execute([$productItemId]);
        $stock = (int)$stmt->fetchColumn();

        // Ambil semua order pending untuk produk ini
        $stmt = $pdo->prepare("
        SELECT id, quantity 
        FROM consum_orders 
        WHERE product_item_id = ? AND status = 'Pending'
        ORDER BY created_at ASC
    ");
        $stmt->execute([$productItemId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $order) {
            $qty = (int)$order['quantity'];
            if ($stock >= $qty) {
                // Update status jadi Ready
                $update = $pdo->prepare("UPDATE consum_orders SET status='Ready', updated_at=NOW() WHERE id=?");
                $update->execute([$order['id']]);

                // Kurangi stok 
                $stock -= $qty;
                $updateStock = $pdo->prepare("UPDATE product_items SET stock=? WHERE id=?");
                $updateStock->execute([$stock, $productItemId]);
            }
        }
    }

    public static function orderNow($customerId, $departmentId, $plantId, $itemId, $qty): array
    {
        $pdo = Database::connect();

        // Ambil detail item lengkap
        $stmt = $pdo->prepare("SELECT id, price, stock, product_type_id, section_id FROM product_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return ['success' => false, 'message' => 'Item tidak ditemukan.'];
        }

        $price     = (float)$item['price'];
        $stock     = (int)$item['stock'];
        $qty       = (int)$qty;
        $status    = ($stock >= $qty) ? 'Ready' : 'Pending';
        $orderCode = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        try {
            $pdo->beginTransaction();

            // Insert order lengkap
            $stmt = $pdo->prepare("
            INSERT INTO consum_orders 
                (order_code, customer_id, product_item_id, product_type_id, section_id, quantity, price, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
            $stmt->execute([
                $orderCode,
                $customerId,
                $item['id'],
                $item['product_type_id'],
                $item['section_id'],
                $qty,
                $price,
                $status
            ]);

            // Kurangi stok jika status Ready
            if ($status === 'Ready') {
                $update = $pdo->prepare("UPDATE product_items SET stock = stock - ? WHERE id = ?");
                $update->execute([$qty, $item['id']]);
            }

            $pdo->commit();
            return ['success' => true, 'message' => 'Order berhasil ditambahkan.'];
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("OrderNow failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memproses order.'];
        }
    }
}
