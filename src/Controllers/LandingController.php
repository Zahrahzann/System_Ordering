<?php
namespace App\Controllers;

use App\Models\ReviewModel;

class LandingController
{
    public function index()
    {
        $reviews = ReviewModel::getAllReviews();
        require __DIR__ . '/../../views/landing_page.php';
    }
}
