<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// BOOTSTRAPPING
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 2. ROUTING
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = str_replace('/system_ordering/public', '', $requestUri); 
if ($route === '') $route = '/';

if (
    $route === '/' ||
    $route === '/logout' ||
    strpos($route, '/tracking') !== false 
) {
    require __DIR__ . '/../routes/web.php';

// Jika bukan rute umum, baru periksa rute khusus peran
} elseif (strpos($route, '/customer/') === 0) {
    require __DIR__ . '/../routes/customer.php';

} elseif (strpos($route, '/spv/') === 0) {
    require __DIR__ . '/../routes/spv.php';

} elseif (strpos($route, '/admin/') === 0) {
    require __DIR__ . '/../routes/admin.php';

} else {
    // Jika tidak cocok sama sekali, tampilkan 404
    http_response_code(404);
    echo "404 Not Found :) <br>";
    echo "Route tidak ditemukan di pemandu arah utama: " . htmlspecialchars($route);
}