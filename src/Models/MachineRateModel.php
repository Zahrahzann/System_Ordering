<?php
namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class MachineRateModel
{
    public static function getRateByProcess(string $processName): float
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT price_per_minute FROM machine_rates WHERE process_name = ?");
        $stmt->execute([$processName]);
        return (float)($stmt->fetchColumn() ?? 0);
    }
}
