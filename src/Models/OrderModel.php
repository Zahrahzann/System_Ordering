<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class OrderModel
{
    /**
     * KOREKSI: Mengambil semua pesanan berdasarkan 'approvals.approval_status'
     */
    public static function getAllPendingItemsForCustomer($customerId)
    {
        $db = Database::connect();

        // KOREKSI: a.status -> a.approval_status
        $sql = "SELECT 
                    o.id AS order_id, 
                    i.production_status AS order_status, 
                    o.created_at AS order_date,
                    a.approval_status as approval_status, 
                    u.name as spv_name,
                    i.item_name, 
                    i.* FROM items i
                JOIN orders o ON i.order_id = o.id
                JOIN approvals a ON o.id = a.order_id
                LEFT JOIN users u ON a.spv_id = u.id
                WHERE 
                    o.customer_id = :customer_id 
                    AND (a.approval_status = 'waiting' OR a.approval_status = 'rejected') 
                ORDER BY 
                    o.created_at DESC, i.item_name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':customer_id' => $customerId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Grouping
        $groupedOrders = [];
        foreach ($items as $item) {
            $orderId = $item['order_id'];
            if (!isset($groupedOrders[$orderId])) {
                $groupedOrders[$orderId] = [
                    'order_details' => [
                        'order_id'       => $item['order_id'],
                        'order_status'   => $item['order_status'],
                        'order_date'     => $item['order_date'],
                        'approval_status' => $item['approval_status'],
                        'spv_name'       => $item['spv_name']
                    ],
                    'items' => []
                ];
            }
            $groupedOrders[$orderId]['items'][] = $item;
        }
        return $groupedOrders;
    }

    /**
     * FUNGSI BARU: Untuk menghapus order yang ditolak
     */
    public static function deleteRejectedOrder($orderId, $customerId)
    {
        $pdo = Database::connect();
        try {
            $pdo->beginTransaction();

            // 1. Cek keamanan
            // KOREKSI: a.status -> a.approval_status
            $stmtCheck = $pdo->prepare(
                "SELECT o.id FROM orders o
                 JOIN approvals a ON o.id = a.order_id
                 WHERE o.id = ? AND o.customer_id = ? AND a.approval_status = 'rejected'"
            );
            $stmtCheck->execute([$orderId, $customerId]);
            if ($stmtCheck->fetch() === false) {
                $pdo->rollBack();
                return false;
            }

            // 2. Hapus item
            $stmtItems = $pdo->prepare("DELETE FROM items WHERE order_id = ?");
            $stmtItems->execute([$orderId]);

            // 3. Hapus approval
            $stmtApproval = $pdo->prepare("DELETE FROM approvals WHERE order_id = ?");
            $stmtApproval->execute([$orderId]);

            // 4. Hapus order
            $stmtOrder = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmtOrder->execute([$orderId]);

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
