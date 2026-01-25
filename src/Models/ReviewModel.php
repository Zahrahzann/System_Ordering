<?php

namespace App\Models;

use ManufactureEngineering\SystemOrdering\Config\Database;
use PDO;

class ReviewModel
{
    /**
     * Ambil semua review yang sudah submitted + nama customer
     */
    public static function getAllReviews()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("
            SELECT r.*, c.name AS customer_name
            FROM reviews r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.status = 'submitted'
              AND r.rating IS NOT NULL
              AND r.review IS NOT NULL
            ORDER BY r.created_at DESC
            LIMIT 7
        ");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

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
     * Jika record belum ada → insert baru
     * Jika sudah ada → update
     */
    public static function submitReview($orderId, $customerId, $rating, $review)
    {
        $pdo = Database::connect();

        // Cek apakah review sudah ada
        $check = $pdo->prepare("
        SELECT id FROM reviews 
        WHERE order_id = ? AND customer_id = ?
    ");
        $check->execute([$orderId, $customerId]);

        if ($check->fetch()) {
            // Sudah ada → update
            $stmt = $pdo->prepare("
            UPDATE reviews 
            SET rating = ?, review = ?, status = 'submitted', created_at = NOW()
            WHERE order_id = ? AND customer_id = ?
        ");
            $stmt->execute([$rating, $review, $orderId, $customerId]);
        } else {
            // Belum ada → insert baru
            $stmt = $pdo->prepare("
            INSERT INTO reviews (order_id, customer_id, rating, review, status, created_at)
            VALUES (?, ?, ?, ?, 'submitted', NOW())
        ");
            $stmt->execute([$orderId, $customerId, $rating, $review]);
        }
    }

    /**
     * Ambil review berdasarkan order + nama customer
     */
    public static function getReviewByOrder($orderId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT r.*, c.name AS customer_name
            FROM reviews r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua review customer tertentu
     */
    public static function getReviewsByCustomer($customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT r.*, c.name AS customer_name
            FROM reviews r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.customer_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hitung rata-rata rating per order
     */
    public static function getAverageRating($orderId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT AVG(r.rating) as avg_rating
            FROM reviews r
            WHERE r.order_id = ? AND r.status = 'submitted'
        ");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['avg_rating'] !== null ? round($row['avg_rating'], 2) : null;
    }

    public static function checkExisting($orderId, $customerId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
        SELECT id 
        FROM reviews 
        WHERE order_id = ? AND customer_id = ? 
          AND status = 'submitted'
    ");
        $stmt->execute([$orderId, $customerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // return row kalau ada, null kalau tidak
    }
}
