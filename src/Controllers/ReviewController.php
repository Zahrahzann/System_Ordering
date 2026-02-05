<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use App\Middleware\SessionMiddleware;

class ReviewController
{
    public static function submit()
    {
        SessionMiddleware::requireLogin();

        header('Content-Type: application/json');

        $orderId    = $_POST['order_id'] ?? null;
        $rating     = trim($_POST['rating'] ?? '');
        $review     = trim($_POST['review'] ?? '');
        $customerId = $_SESSION['user_data']['id'] ?? null;

        if (!$orderId || !$rating || !$review || !$customerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $ok = ReviewModel::submitReview($orderId, $customerId, $rating, $review);

        if ($ok) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan review']);
        }
        exit;
    }
}
