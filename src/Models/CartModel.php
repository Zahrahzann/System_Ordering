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
            "SELECT i.*, 
                    mt.name AS material_type, 
                    mt.material_number AS material_number,
                    md.dimension AS material_dimension
             FROM items i
             LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id
             LEFT JOIN material_types mt ON md.material_type_id = mt.id
             WHERE i.customer_id = ? AND i.order_id IS NULL
             ORDER BY i.created_at DESC"
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getItemById($itemId, $customerId)
    {
        $pdo = Database::connect();
        $sql = "SELECT i.*, 
                   mt.name AS material_type, 
                   mt.material_number AS material_number,
                   md.dimension AS material_dimension
            FROM items i
            LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id
            LEFT JOIN material_types mt ON md.material_type_id = mt.id
            WHERE i.id = ? AND i.customer_id = ? AND i.order_id IS NULL
            LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$itemId, $customerId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ?: false;
    }

    /**
     * Mengambil item berdasarkan array ID (untuk checkout/konfirmasi).
     */
    public static function getItemsByIds($customerId, array $itemIds)
    {
        if (empty($itemIds)) {
            return [];
        }
        $pdo = Database::connect();
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $sql = "SELECT i.*, 
                       mt.name AS material_type, 
                       mt.material_number AS material_number,
                       md.dimension AS material_dimension
                FROM items i
                LEFT JOIN material_dimensions md ON i.material_dimension_id = md.id
                LEFT JOIN material_types mt ON md.material_type_id = mt.id
                WHERE i.customer_id = ? 
                  AND i.id IN ($placeholders) 
                  AND i.order_id IS NULL";
        $stmt = $pdo->prepare($sql);
        $params = array_merge([$customerId], $itemIds);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Meng-update data item di keranjang.
     */
    public static function updateItem($itemId, array $data, $customerId)
    {
        $pdo = Database::connect();
        $sql = "UPDATE items SET
                item_name = :item_name, 
                category = :category, 
                quantity = :quantity,
                material_status = :material_status, 
                material_dimension_id = :material_dimension_id,
                file_path = :file_path, 
                needed_date = :needed_date,
                note = :note, 
                is_emergency = :is_emergency, 
                emergency_type = :emergency_type
            WHERE id = :item_id AND customer_id = :customer_id";

        $stmt = $pdo->prepare($sql);

        // Pastikan semua key ada, walau nilainya null
        $dataToBind = [
            'item_name'             => $data['item_name'] ?? null,
            'category'              => $data['category'] ?? null,
            'quantity'              => $data['quantity'] ?? null,
            'material_status'       => $data['material_status'] ?? null,
            'material_dimension_id' => $data['material_dimension_id'] ?? null,
            'file_path'             => $data['file_path'] ?? null,
            'needed_date'           => $data['needed_date'] ?? null,
            'note'                  => $data['note'] ?? null,
            'is_emergency'          => $data['is_emergency'] ?? null,
            'emergency_type'        => $data['emergency_type'] ?? null,
            'item_id'               => $itemId,
            'customer_id'           => $customerId,
        ];

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

            // 2. Buat record di tabel 'orders'
            $stmtOrder = $pdo->prepare(
                "INSERT INTO orders (customer_id, department, plant_id, approval_status, created_at)
     VALUES (?, ?, ?, 'waiting', NOW())"
            );
            $stmtOrder->execute([$customerId, $customerDeptId, $customerPlantId]);
            $orderId = $pdo->lastInsertId();

            $stmtApproval = $pdo->prepare("INSERT INTO approvals (order_id, spv_id, approval_status) VALUES (?, ?, 'waiting')");
            $stmtApproval->execute([$orderId, $spvId]);

            // 3. Update items → assign ke order
            $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
            $sqlItems = "UPDATE items SET order_id = ? 
                         WHERE customer_id = ? 
                           AND id IN ($placeholders) 
                           AND order_id IS NULL";
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
            error_log('Checkout Error: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Menambahkan item baru ke keranjang.
     */
    public static function addItemToCart($customerId, array $item)
    {
        $pdo = Database::connect();

        $sql = "INSERT INTO items (
                    customer_id, item_name, quantity, category, material_status, material_dimension_id,
                    file_path, needed_date, note, is_emergency, emergency_type, item_type, created_at 
                ) VALUES (
                    :customer_id, :item_name, :quantity, :category, :material_status, :material_dimension_id,
                    :file_path, :needed_date, :note, :is_emergency, :emergency_type, :item_type, NOW()
                )";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'customer_id'          => $customerId,
            'item_name'            => $item['item_name'],
            'quantity'             => $item['quantity'],
            'category'             => $item['category'],
            'material_status'      => $item['material_status'] ?? null,
            'material_dimension_id' => $item['material_dimension_id'] ?? null,
            'file_path'            => $item['file_path'] ?? null,
            'needed_date'          => date('Y-m-d'),
            'note'                 => $item['note'] ?? null,
            'is_emergency'         => $item['is_emergency'] ?? 0,
            'emergency_type'       => $item['emergency_type'] ?? null,
            'item_type'            => $item['item_type'] ?? 'work_order'
        ]);
    }
}
