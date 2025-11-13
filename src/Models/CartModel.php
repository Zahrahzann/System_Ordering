<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class CartModel
{
    /**
     * Mengambil semua item di keranjang milik customer tertentu.
     */
    public static function getItems($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM items WHERE customer_id = ? AND order_id IS NULL ORDER BY created_at DESC"
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil satu item berdasarkan ID dan ID customer (untuk keamanan).
     */
    public static function getItemById($itemId, $customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM items WHERE id = ? AND customer_id = ? AND order_id IS NULL"
        );
        $stmt->execute([$itemId, $customerId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ?: false; // Kembalikan false jika tidak ditemukan
    }

    /**
     * Meng-update data item di keranjang.
     */
    public static function updateItem($itemId, array $data, $customerId)
    {
        $pdo = Database::connect();
        $sql = "UPDATE items SET
                        item_name = :item_name, category = :category, quantity = :quantity,
                        material = :material, material_type = :material_type, file_path = :file_path, needed_date = :needed_date,
                        note = :note, is_emergency = :is_emergency, emergency_type = :emergency_type
                    WHERE id = :item_id AND customer_id = :customer_id";

        $stmt = $pdo->prepare($sql);
        $dataToBind = $data;
        $dataToBind['item_id'] = $itemId;
        $dataToBind['customer_id'] = $customerId;
        return $stmt->execute($dataToBind);
    }

    /**
     * Menghapus item dari keranjang.
     */
    public static function deleteItem($itemId, $customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "DELETE FROM items WHERE id = ? AND customer_id = ? AND order_id IS NULL"
        );
        return $stmt->execute([$itemId, $customerId]);
    }

    /**
     * Mengambil item berdasarkan array ID.
     */
    public static function getItemsByIds($customerId, array $itemIds)
    {
        if (empty($itemIds)) {
            return [];
        }
        $pdo = Database::connect();
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $sql = "SELECT * FROM items WHERE customer_id = ? AND id IN ($placeholders) AND order_id IS NULL";
        $stmt = $pdo->prepare($sql);
        $params = array_merge([$customerId], $itemIds);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Memproses checkout sebagai sebuah transaksi database.
     */
    public static function checkout($customerId, $customerDeptId, $customerPlantId, array $itemIds)
    {
        if (empty($itemIds)) {
            return ['success' => false, 'message' => 'Gagal: Tidak ada item yang dipilih.'];
        }

        $pdo = Database::connect();
        try {
            $pdo->beginTransaction();

            // 1. Cari SPV yang sesuai
            $stmtSpv = $pdo->prepare("SELECT id FROM users WHERE role = 'spv' AND department_id = ? AND plant_id = ? LIMIT 1");
            $stmtSpv->execute([$customerDeptId, $customerPlantId]);
            $spv = $stmtSpv->fetch(PDO::FETCH_ASSOC);

            if (!$spv) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Gagal: SPV Approval untuk departemen & plant Anda tidak ditemukan. Hubungi admin.'];
            }
            $spvId = $spv['id'];
            
            // 2. Buat record di tabel 'orders' (tanpa production_status, karena itu per-item di tabel items)
            $stmtOrder = $pdo->prepare(
                "INSERT INTO orders (customer_id, plant_id) VALUES (?, ?)"
            );
            $stmtOrder->execute([$customerId, $customerPlantId]);
            $orderId = $pdo->lastInsertId();

            $stmtApproval = $pdo->prepare("INSERT INTO approvals (order_id, spv_id, approval_status) VALUES (?, ?, 'waiting')");
            $stmtApproval->execute([$orderId, $spvId]);
            // ==========================================================
            $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
            $sqlItems = "UPDATE items SET order_id = ? WHERE customer_id = ? AND id IN ($placeholders) AND order_id IS NULL";
            $stmtItems = $pdo->prepare($sqlItems);
            $params = array_merge([$orderId, $customerId], $itemIds);
            $stmtItems->execute($params);

            if ($stmtItems->rowCount() === 0) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Gagal: Item yang dipilih tidak valid atau sudah di-checkout.'];
            }

            $pdo->commit();
            return ['success' => true, 'message' => 'Checkout berhasil! Order Anda sedang menunggu approval SPV.'];
        } catch (\Exception $e) {
            $pdo->rollBack();
            // Debug: log error ke file untuk visibility
            error_log('Checkout Error: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return ['success' => false, 'message' => 'Error Sebenarnya: ' . $e->getMessage()];
        }
    }
}