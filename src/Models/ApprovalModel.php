<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ApprovalModel
{
    /**
     * Ambil semua order untuk SPV berdasarkan departemen (waiting + reject).
     */
    public static function getOrdersForSpv($spvId)
    {
        $pdo = Database::connect();

        // Ambil departemen SPV
        $sqlDept = "SELECT department_id FROM users WHERE id = ?";
        $stmtDept = $pdo->prepare($sqlDept);
        $stmtDept->execute([$spvId]);
        $departmentId = $stmtDept->fetchColumn();

        $sql = "SELECT 
                    o.id as order_id, 
                    o.created_at, 
                    c.name as customer_name, 
                    c.line as line,
                    d.name as department_name, 
                    o.approval_status
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                WHERE d.id = ?
                  AND o.approval_status IN ('waiting','reject')
                ORDER BY o.created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hitung jumlah order berdasarkan status untuk SPV (berdasarkan departemen).
     */
    public static function countByStatusForSpv($spvId, $status)
    {
        $pdo = Database::connect();

        // Ambil departemen SPV
        $sqlDept = "SELECT department_id FROM users WHERE id = ?";
        $stmtDept = $pdo->prepare($sqlDept);
        $stmtDept->execute([$spvId]);
        $departmentId = $stmtDept->fetchColumn();

        $sql = "SELECT COUNT(*) 
                  FROM orders o
                  JOIN customers c ON o.customer_id = c.id
                 WHERE c.department_id = ? AND o.approval_status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$departmentId, $status]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil detail order by ID.
     */
    public static function findOrderById($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT o.*, 
                       c.name as customer_name, 
                       c.npk as customer_npk, 
                       c.line, 
                       d.name as department_name, 
                       p.name as plant_name
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                JOIN departments d ON c.department_id = d.id
                JOIN plants p ON o.plant_id = p.id
                WHERE o.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua item dari order.
     */
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

    /**
     * Update status approval (approve/reject) + sinkron ke tabel orders.
     */
    public static function updateApprovalStatus($orderId, $spvId, $status, $notes)
    {
        $pdo = Database::connect();

        // Ambil id approval terbaru untuk order + spv ini
        $sql = "SELECT id FROM approvals 
            WHERE order_id = :order_id AND spv_id = :spv_id 
            ORDER BY updated_at DESC 
            LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':spv_id'   => $spvId
        ]);
        $approvalId = $stmt->fetchColumn();

        if ($approvalId) {
            // Update baris approval yang ketemu
            $sqlUpdate = "UPDATE approvals 
                      SET approval_status = :status, comments = :notes, updated_at = NOW()
                      WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $ok = $stmtUpdate->execute([
                ':status' => $status,
                ':notes'  => $notes,
                ':id'     => $approvalId
            ]);
        } else {
            // Kalau belum ada baris approval, buat baru
            $sqlInsert = "INSERT INTO approvals (order_id, spv_id, approval_status, comments, created_at, updated_at)
                      VALUES (:order_id, :spv_id, :status, :notes, NOW(), NOW())";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $ok = $stmtInsert->execute([
                ':order_id' => $orderId,
                ':spv_id'   => $spvId,
                ':status'   => $status,
                ':notes'    => $notes
            ]);
        }

        // Sinkron ke tabel orders
        if ($ok) {
            self::updateOrderStatus($orderId);
        }

        return $ok;
    }

    /**
     * Update status di tabel orders.
     */
    public static function updateOrderStatus($orderId)
    {
        $pdo = Database::connect();

        // Ambil approval terbaru untuk order ini
        $sql = "SELECT approval_status 
              FROM approvals 
             WHERE order_id = ? 
          ORDER BY updated_at DESC 
             LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $status = $stmt->fetchColumn();

        if (!$status) {
            $status = 'waiting'; // default kalau belum ada approval
        }

        // Update status di tabel orders sesuai approval terbaru
        $sqlUpdate = "UPDATE orders SET approval_status = ? WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        return $stmtUpdate->execute([$status, $orderId]);
    }

    /**
     * Ambil data approval by order.
     */
    public static function findApprovalByOrderId($orderId)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                a.approval_status, 
                a.comments, 
                a.updated_at,
                u.name as spv_name
            FROM approvals a
       LEFT JOIN users u ON a.spv_id = u.id
           WHERE a.order_id = :orderId
        ORDER BY a.updated_at DESC
           LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createApprovalEntry($orderId, $spvId)
    {
        $pdo = Database::connect();

        // Cek apakah approval sudah ada untuk order + spv ini
        $checkSql = "SELECT COUNT(*) FROM approvals WHERE order_id = :order_id AND spv_id = :spv_id";
        $stmtCheck = $pdo->prepare($checkSql);
        $stmtCheck->execute([
            ':order_id' => $orderId,
            ':spv_id'   => $spvId
        ]);
        $exists = $stmtCheck->fetchColumn();

        if ($exists == 0) {
            $sql = "INSERT INTO approvals (order_id, spv_id, approval_status, created_at, updated_at)
                VALUES (:order_id, :spv_id, 'waiting', NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':order_id' => $orderId,
                ':spv_id'   => $spvId
            ]);
        }

        return true;
    }
}
