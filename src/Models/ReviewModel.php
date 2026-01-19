<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ReviewModel
{
    /**
     * Tandai order butuh review (status = pending)
     */
    public static function markPendingReview($orderId, $customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            INSERT INTO reviews (order_id, customer_id, status) 
            VALUES (?, ?, 'pending')
            ON DUPLICATE KEY UPDATE status = 'pending'
        ");
        $stmt->execute([$orderId, $customerId]);
    }

    /**
     * Submit review dari customer
     */
    public static function submitReview($orderId, $customerId, $rating, $review)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            UPDATE reviews 
            SET rating = ?, review = ?, status = 'submitted', created_at = NOW()
            WHERE order_id = ? AND customer_id = ?
        ");
        $stmt->execute([$rating, $review, $orderId, $customerId]);
    }

    /**
     * Ambil review berdasarkan order
     */
    public static function getReviewByOrder($orderId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM reviews WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua review customer tertentu
     */
    public static function getReviewsByCustomer($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM reviews WHERE customer_id = ? ORDER BY created_at DESC");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Hitung rata-rata rating per order
     */
    public static function getAverageRating($orderId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? round($row['avg_rating'], 2) : null;
    }
}
