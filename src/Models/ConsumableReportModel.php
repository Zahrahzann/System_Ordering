<?php

namespace App\Models;

use PDO;
use ManufactureEngineering\SystemOrdering\Config\Database;

class ConsumableReportModel
{
    private static function db(): PDO
    {
        return Database::connect();
    }

    // Simpan qty bulanan per item (dipanggil saat order selesai)
    public static function saveQty(int $sectionId, int $productTypeId, int $itemId, int $month, int $year, int $qty): void
    {
        $pdo = self::db();

        // Ambil harga dari product_items
        $st = $pdo->prepare("SELECT price, maker_price FROM product_items WHERE id = ?");
        $st->execute([$itemId]);
        $item = $st->fetch(PDO::FETCH_ASSOC);

        $inhouse = (float)($item['price'] ?? 0);
        $maker   = (float)($item['maker_price'] ?? 0);

        $insert = $pdo->prepare("
            INSERT INTO consumable_reports 
                (section_id, product_type_id, product_item_id, month, year, qty, inhouse_price, maker_price, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
                qty = qty + VALUES(qty),
                inhouse_price = VALUES(inhouse_price),
                maker_price = VALUES(maker_price)
        ");
        $insert->execute([$sectionId, $productTypeId, $itemId, $month, $year, $qty, $inhouse, $maker]);
    }

    // Ambil report otomatis dari order selesai
    public static function getReport(?int $month, int $year): array
    {
        $pdo = self::db();
        $params = [$year];

        $sql = "
        SELECT 
            s.id AS section_id,
            s.name AS section_name,
            " . (is_null($month) ? "MONTH(o.updated_at) AS month," : "") . "
            COALESCE(SUM(o.quantity), 0) AS qty,
            COALESCE(MAX(o.price), 0) AS inhouse_price,
            COALESCE(MAX(pi.maker_price), 0) AS maker_price,
            COALESCE(SUM(o.quantity * o.price), 0) AS total_inhouse,
            COALESCE(SUM(o.quantity * pi.maker_price), 0) AS total_maker,
            COALESCE(SUM(o.quantity * (pi.maker_price - o.price)), 0) AS benefit
        FROM sections s
        LEFT JOIN product_types pt ON pt.section_id = s.id
        LEFT JOIN product_items pi ON pi.product_type_id = pt.id
        LEFT JOIN consum_orders o 
            ON o.product_item_id = pi.id 
           AND o.status = 'Selesai'
           AND YEAR(o.updated_at) = ?
    ";

        if (!is_null($month)) {
            $sql .= " AND MONTH(o.updated_at) = ?";
            $params[] = $month;
        }

        $sql .= " GROUP BY s.id, s.name";
        if (is_null($month)) {
            $sql .= ", MONTH(o.updated_at)";
        }

        $sql .= " ORDER BY s.name";
        if (is_null($month)) {
            $sql .= ", MONTH(o.updated_at)";
        }

        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
