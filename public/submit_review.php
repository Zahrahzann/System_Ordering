<?php
session_start();
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Models/ReviewModel.php';

use App\Models\ReviewModel;

header('Content-Type: application/json'); 

$orderId    = $_POST['order_id'] ?? null;
$rating     = $_POST['rating'] ?? null;
$reviewText = trim($_POST['review'] ?? '');
$customerId = $_SESSION['user_data']['id'] ?? null;

if ($orderId && $rating && $reviewText && $customerId) {
    ReviewModel::submitReview($orderId, $customerId, $rating, $reviewText);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Terima Kasih, Kami menunggu pesananmu yang lainnya lagi~!'
    ]);
    exit;
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Data review tidak lengkap.'
    ]);
    exit;
}
