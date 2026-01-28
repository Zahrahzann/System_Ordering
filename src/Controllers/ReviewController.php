<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use App\Middleware\SessionMiddleware;

class ReviewController
{
    public static function submit()
    {
        SessionMiddleware::requireLogin();

        // --- DEBUG: cek isi POST dan SESSION ---
        error_log("REVIEW POST: " . print_r($_POST, true));
        error_log("REVIEW SESSION: " . print_r($_SESSION, true));

        $orderId    = $_POST['order_id'] ?? null;
        $rating     = $_POST['rating'] ?? null;
        $review     = $_POST['review'] ?? null;
        $customerId = $_SESSION['user_data']['id'] ?? null;

        // Validasi input
        if (!$orderId || !$rating || !$review || !$customerId) {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            exit;
        }

        ReviewModel::submitReview($orderId, $customerId, $rating, $review);

        echo json_encode(['success' => true]);
        exit;
    }
}
