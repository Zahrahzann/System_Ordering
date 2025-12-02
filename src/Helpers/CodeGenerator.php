<?php

namespace App\Helpers;

class CodeGenerator
{
    public static function generateProductCode($sectionName, $productName)
    {
        $sectionSlug = strtoupper(substr(preg_replace('/\s+/', '', $sectionName), 0, 5));
        $productSlug = strtoupper(substr(preg_replace('/\s+/', '', $productName), 0, 5));
        $randomNumber = mt_rand(1000, 9999);

        return $sectionSlug . '-' . $randomNumber . '-' . $productSlug;
    }

    // Kalau nanti mau tambah:
    public static function generateItemCode($productTypeName, $itemName)
    {
        $typeSlug = strtoupper(substr(preg_replace('/\s+/', '', $productTypeName), 0, 5));
        $itemSlug = strtoupper(substr(preg_replace('/\s+/', '', $itemName), 0, 5));
        $randomNumber = mt_rand(1000, 9999);

        return $typeSlug . '-' . $randomNumber . '-' . $itemSlug;
    }
}
