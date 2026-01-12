<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class WorkOrderCostModel
{
    /**
     * Simpan detail proses lengkap ke workorder_processes
     */
    public static function saveFullProcess(
        int $orderId,
        string $machineProcess,
        int $machineTime,
        float $machineRate,
        string $manpowerProcess,
        int $manpowerTime,
        float $manpowerRate,
        float $materialCost,
        float $vendorPricePerPcs
    ): void {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $machineCost  = $machineRate  * $machineTime;
        $manpowerCost = $manpowerRate * $manpowerTime;

        $stmt = $pdo->prepare("
            INSERT INTO workorder_processes
            (order_id, machine_process, machine_time, machine_cost,
             manpower_process, manpower_time, manpower_cost,
             material_cost, vendor_price_per_pcs, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $orderId,
            $machineProcess,
            $machineTime,
            $machineCost,
            $manpowerProcess,
            $manpowerTime,
            $manpowerCost,
            $materialCost,
            $vendorPricePerPcs
        ]);
    }

    /**
     * Insert/Update summary ke tabel workorder_costs (hanya untuk Admin)
     */
    public static function saveWorkOrderCost(
        int $orderId,
        string $itemName,
        ?string $departmentId,
        ?int $customerId,
        string $status,
        float $materialCost,
        float $vendorPricePerPcs,
        float $machineTotal,
        float $manpowerTotal
    ): void {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ambil qty dari items
        $stmt = $pdo->prepare("SELECT quantity FROM items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $qty = (int)$stmt->fetchColumn();
        if ($qty <= 0) {
            $qty = 1; // fallback aman
        }

        // Hitung biaya summary
        $baseCost    = $materialCost + $machineTotal + $manpowerTotal;
        $overhead    = 0.10 * $baseCost;
        $costPerPcs  = $baseCost + $overhead;
        $costInhouse = $costPerPcs * $qty;
        $vendorTotal = $vendorPricePerPcs * $qty;
        $benefit     = $vendorTotal - $costInhouse;
        $reportYear  = (int)date('Y');

        // Cek apakah sudah ada summary untuk order ini
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM workorder_costs WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $exists = ((int)$stmt->fetchColumn()) > 0;

        if ($exists) {
            $stmt = $pdo->prepare("
                UPDATE workorder_costs
                SET item_name = ?, department_id = ?, customer_id = ?,
                    qty = ?, cost_material = ?, cost_machine_tool_electric = ?, cost_manpower = ?,
                    overhead = ?, cost_per_pcs = ?, cost_inhouse_total = ?,
                    vendor_price_per_pcs = ?, vendor_price_total = ?, benefit = ?,
                    status = ?, report_year = ?, updated_at = NOW()
                WHERE order_id = ?
            ");
            $stmt->execute([
                $itemName,
                $departmentId,
                $customerId,
                $qty,
                $materialCost,
                $machineTotal,
                $manpowerTotal,
                $overhead,
                $costPerPcs,
                $costInhouse,
                $vendorPricePerPcs,
                $vendorTotal,
                $benefit,
                $status,
                $reportYear,
                $orderId
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO workorder_costs
                (order_id, item_name, department_id, customer_id,
                 qty, cost_material, cost_machine_tool_electric, cost_manpower,
                 overhead, cost_per_pcs, cost_inhouse_total,
                 vendor_price_per_pcs, vendor_price_total, benefit,
                 status, report_year, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $orderId,
                $itemName,
                $departmentId,
                $customerId,
                $qty,
                $materialCost,
                $machineTotal,
                $manpowerTotal,
                $overhead,
                $costPerPcs,
                $costInhouse,
                $vendorPricePerPcs,
                $vendorTotal,
                $benefit,
                $status,
                $reportYear
            ]);
        }
    }

    /**
     * Report detail dari tabel proses (dipakai semua role untuk grafik)
     */
    public static function getReportDirect(int $year, ?int $month = null): array
    {
        $pdo = Database::connect();
        $params = [$year];

        $sql = "
            SELECT 
                o.id AS order_id,
                i.item_name,
                i.quantity AS qty,
                i.customer_id,
                i.category AS department_id,
                SUM(p.machine_cost) AS cost_machine,
                SUM(p.manpower_cost) AS cost_manpower,
                SUM(p.material_cost) AS cost_material,
                SUM(p.machine_cost + p.manpower_cost + p.material_cost) AS total_process_cost,
                MAX(p.created_at) AS completed_at
            FROM orders o
            LEFT JOIN workorder_processes p ON o.id = p.order_id
            LEFT JOIN items i ON i.order_id = o.id
            WHERE YEAR(p.created_at) = ?
        ";

        if (!is_null($month)) {
            $sql .= " AND MONTH(p.created_at) = ?";
            $params[] = $month;
        }

        $sql .= " GROUP BY o.id, i.item_name, i.quantity, i.customer_id, i.category
                  ORDER BY completed_at ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Report summary dari tabel workorder_costs
     */
    public static function getSummaryReport(int $year, ?int $month = null): array
    {
        $pdo = Database::connect();
        $params = [$year];

        $sql = "SELECT * FROM workorder_costs WHERE report_year = ?";
        if (!is_null($month)) {
            $sql .= " AND MONTH(updated_at) = ?";
            $params[] = $month;
        }
        $sql .= " ORDER BY updated_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Report per departemen (SPV) – tetap ambil dari proses detail
     */
    // public static function getMonthlyReportByDept(int $year, ?int $month, string $departmentName): array
    // {
    //     $pdo = Database::connect();
    //     $params = [$year, $departmentName];

    //     $sql = "
    //         SELECT 
    //             o.id AS order_id,
    //             i.item_name,
    //             i.quantity AS qty,
    //             i.customer_id,
    //             i.category AS department_id,
    //             SUM(p.machine_cost) AS cost_machine,
    //             SUM(p.manpower_cost) AS cost_manpower,
    //             SUM(p.material_cost) AS cost_material,
    //             SUM(p.machine_cost + p.manpower_cost + p.material_cost) AS total_process_cost,
    //             MAX(p.created_at) AS completed_at
    //         FROM orders o
    //         LEFT JOIN workorder_processes p ON o.id = p.order_id
    //         LEFT JOIN items i ON i.order_id = o.id
    //         WHERE YEAR(p.created_at) = ? AND i.category = ?
    //     ";

    //     if (!is_null($month)) {
    //         $sql .= " AND MONTH(p.created_at) = ?";
    //         $params[] = $month;
    //     }

    //     $sql .= " GROUP BY o.id, i.item_name, i.quantity, i.customer_id, i.category
    //               ORDER BY completed_at ASC";

    //     $stmt = $pdo->prepare($sql);
    //     $stmt->execute($params);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    // }

    // /**
    //  * Report per customer – sekarang juga ambil dari proses detail
    //  */
    // public static function getMonthlyReportByCustomer(int $year, ?int $month, int $customerId): array
    // {
    //     $pdo = Database::connect();
    //     $params = [$year, $customerId];

    //     $sql = "
    //         SELECT 
    //             o.id AS order_id,
    //             i.item_name,
    //             i.quantity AS qty,
    //             i.customer_id,
    //             i.category AS department_id,
    //             SUM(p.machine_cost) AS cost_machine,
    //             SUM(p.manpower_cost) AS cost_manpower,
    //             SUM(p.material_cost) AS cost_material,
    //             SUM(p.machine_cost + p.manpower_cost + p.material_cost) AS total_process_cost,
    //             MAX(p.created_at) AS completed_at
    //         FROM orders o
    //         LEFT JOIN workorder_processes p ON o.id = p.order_id
    //         LEFT JOIN items i ON i.order_id = o.id
    //         WHERE YEAR(p.created_at) = ? AND i.customer_id = ?
    //     ";

    //     if (!is_null($month)) {
    //         $sql .= " AND MONTH(p.created_at) = ?";
    //         $params[] = $month;
    //     }

    //     $sql .= " GROUP BY o.id, i.item_name, i.quantity, i.customer_id, i.category
    //               ORDER BY completed_at ASC";

    //     $stmt = $pdo->prepare($sql);
    //     $stmt->execute($params);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    // }

}
