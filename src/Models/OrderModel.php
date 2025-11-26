<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class OrderModel
{
    public static function countByStatus($status)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM orders o
        JOIN approvals a ON o.id = a.order_id
        WHERE a.approval_status = :status
    ");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }


    public static function getRecentOrders($limit = 5)
    {
        $pdo = Database::connect();
        $limit = (int) $limit; // pastikan integer
        $sql = "SELECT o.order_code, o.created_at AS date, a.approval_status AS status, u.name AS requested_by
            FROM orders o
            JOIN approvals a ON o.id = a.order_id
            LEFT JOIN users u ON o.customer_id = u.id
            ORDER BY o.created_at DESC 
            LIMIT $limit";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Mengambil semua pesanan berdasarkan 'approvals.approval_status'
     */
    public static function getAllPendingItemsForCustomer($customerId)
    {
        $db = Database::connect();
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
                    AND (a.approval_status = 'waiting' OR a.approval_status = 'reject') 
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
                 WHERE o.id = ? AND o.customer_id = ? AND a.approval_status = 'reject'"
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

    /**
     * Mengambil jumlah Work Order masuk per bulan (berdasarkan created_at)
     */
    public static function getMonthlyWoInData(int $year): array
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT o.id) AS total_in
            FROM orders o
            WHERE YEAR(o.created_at) = :year
            GROUP BY MONTH(o.created_at)
            ORDER BY MONTH(o.created_at)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthlyData[(int)$row['month']] = (int)$row['total_in'];
        }

        return $monthlyData;
    }

    public static function getAllItemsForCustomer($customerId)
    {
        $pdo = Database::connect();
        $sql = "SELECT o.id, o.created_at, a.approval_status, u.name as spv_name
            FROM orders o
            JOIN approvals a ON o.id = a.order_id
            LEFT JOIN users u ON a.spv_id = u.id
            WHERE o.customer_id = ?
              AND a.approval_status IN ('waiting','reject')
            ORDER BY o.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($orders as $order) {
            $items = self::findOrderItemsByOrderId($order['id']);
            $result[] = [
                'order_details' => [
                    'order_id'        => $order['id'],
                    'order_date'      => $order['created_at'],
                    'approval_status' => $order['approval_status'],
                    'spv_name'        => $order['spv_name'] ?? null
                ],
                'items' => $items
            ];
        }
        return $result;
    }

    public static function findOrderItemsByOrderId($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT * FROM items WHERE order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMonthlyWoPendingData(int $year): array
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT i.order_id) AS total_pending
            FROM orders o
            JOIN items i ON o.id = i.order_id
            JOIN approvals a ON o.id = a.order_id
            WHERE YEAR(o.created_at) = :year
              AND a.approval_status = 'approve'
              AND i.production_status = 'pending'
            GROUP BY MONTH(o.created_at)
            ORDER BY MONTH(o.created_at)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthlyData[(int)$row['month']] = (int)$row['total_pending'];
        }

        return $monthlyData;
    }

    public static function getMonthlyWoCompletedData(int $year): array
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT o.id) AS total_completed
            FROM orders o
            JOIN items i ON o.id = i.order_id
            WHERE YEAR(o.created_at) = :year
              AND i.production_status = 'completed'
            GROUP BY MONTH(o.created_at)
            ORDER BY MONTH(o.created_at)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthlyData[(int)$row['month']] = (int)$row['total_completed'];
        }
        return $monthlyData;
    }

    public static function getMonthlyWoOnProgress(int $year): array
    {
        $pdo = Database::connect();

        $sql = "SELECT
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT o.id) AS total_onProgress
            FROM orders o
            JOIN items i ON o.id = i.order_id
            WHERE YEAR(o.created_at) = :year
                AND i.production_status = 'on_progress'
            GROUP BY MONTH(o.created_at)
            ORDER BY MONTH(o.created_at)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthlyData[(int)$row['month']] = (int)$row['total_onProgress'];
        }
        return $monthlyData;
    }
    public static function getMonthlyWoFinishData(int $year): array
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT o.id) AS total_finish
            FROM orders o
            JOIN items i ON o.id = i.order_id
            WHERE YEAR(o.created_at) = :year
              AND i.production_status = 'finish'
            GROUP BY MONTH(o.created_at)
            ORDER BY MONTH(o.created_at)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':year' => $year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthlyData[(int)$row['month']] = (int)$row['total_finish'];
        }
        return $monthlyData;
    }
}
