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
        SELECT COUNT(DISTINCT o.id) 
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
        $limit = (int) $limit;

        $sql = "SELECT 
                o.order_code, 
                o.created_at AS date,
                (SELECT a.approval_status 
                 FROM approvals a 
                 WHERE a.order_id = o.id 
                 ORDER BY a.updated_at DESC 
                 LIMIT 1) AS status,
                u.name AS requested_by
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            ORDER BY o.created_at DESC 
            LIMIT $limit";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil semua pesanan berdasarkan 'approvals.approval_status'
     */
    public static function getAllPendingOrdersForCustomer($customerId)
    {
        $db = Database::connect();

        $sqlOrders = "SELECT 
                     o.id AS order_id,
                     o.created_at AS order_date,
                     (SELECT a.approval_status 
                      FROM approvals a 
                      WHERE a.order_id = o.id 
                      ORDER BY a.updated_at DESC 
                      LIMIT 1) AS approval_status,
                     (SELECT u.name 
                      FROM approvals a 
                      JOIN users u ON a.spv_id = u.id 
                      WHERE a.order_id = o.id 
                      ORDER BY a.updated_at DESC 
                      LIMIT 1) AS spv_name
                  FROM orders o
                  WHERE o.customer_id = :customer_id
                    AND o.approval_status IN ('waiting','reject','approve')
                  ORDER BY o.created_at DESC";

        $stmtOrders = $db->prepare($sqlOrders);
        $stmtOrders->execute([':customer_id' => $customerId]);
        $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($orders as $order) {
            $sqlItems = "SELECT i.item_name, i.quantity, i.is_emergency, i.emergency_type,
                            mt.name AS material_type, mt.material_number, md.dimension AS material_dimension
                     FROM items i
                     LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id
                     LEFT JOIN material_types mt ON md.material_type_id = mt.id
                     WHERE i.order_id = :order_id
                     ORDER BY i.item_name ASC";
            $stmtItems = $db->prepare($sqlItems);
            $stmtItems->execute([':order_id' => $order['order_id']]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $result[] = [
                'order_details' => $order,
                'items' => $items
            ];
        }

        return $result;
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
     * Mengambil jumlah Work Order masuk per bulan (hanya yang sudah di-approve SPV)
     */
    public static function getMonthlyWoInData(int $year): array
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT o.id) AS total_in
            FROM orders o
            JOIN approvals a ON o.id = a.order_id
            WHERE YEAR(o.created_at) = :year
              AND a.approval_status = 'approve'
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

        // Ambil order unik saja
        $sql = "SELECT o.id, o.created_at, o.approval_status
            FROM orders o
            WHERE o.customer_id = ?
              AND o.approval_status IN ('waiting','reject')
            ORDER BY o.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($orders as $order) {
            $items = self::findOrderItemsByOrderId($order['id']);

            // Ambil satu approval detail (misalnya SPV pertama) kalau perlu
            $approval = ApprovalModel::findApprovalByOrderId($order['id']);

            $result[] = [
                'order_details' => [
                    'order_id'        => $order['id'],
                    'order_date'      => $order['created_at'],
                    'approval_status' => $order['approval_status'],
                    'spv_name'        => $approval['spv_name'] ?? null
                ],
                'items' => $items
            ];
        }

        return $result;
    }

    public static function findOrderItemsByOrderId($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT i.*, 
                   mt.name AS material_type, 
                   mt.material_number AS material_number,
                   md.dimension AS material_dimension
            FROM items i
            LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id
            LEFT JOIN material_types mt ON md.material_type_id = mt.id
            WHERE i.order_id = ?";
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
