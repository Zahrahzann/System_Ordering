<?php
require_once __DIR__ . '/../vendor/autoload.php';
use ManufactureEngineering\SystemOrdering\Config\Database;

$pdo = Database::connect();
$stmt = $pdo->query("SELECT id, dimension, stock, minimum_stock 
                     FROM material_dimensions 
                     WHERE stock <= minimum_stock");
$lowStocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($lowStocks)) {
    $stock = (int)$lowStocks[0]['stock'];          
    $minStock = (int)$lowStocks[0]['minimum_stock']; 

    echo json_encode([
        'alert' => true,
        'id' => $lowStocks[0]['id'],
        'message' => "Stok material '{$lowStocks[0]['dimension']}' sudah mencapai batas minimum ({$stock} ≤ {$minStock})"
    ]);
} else {
    echo json_encode(['alert' => false]);
}
