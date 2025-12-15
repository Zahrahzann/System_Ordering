<?php

namespace App\Helpers;

class ExportHelper
{
    public static function exportToExcel(string $filename, array $headers, array $rows)
    {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$filename}");

        // Header kolom
        echo implode("\t", $headers) . "\n";

        // Data baris
        foreach ($rows as $row) {
            echo implode("\t", $row) . "\n";
        }
        exit;
    }
}
