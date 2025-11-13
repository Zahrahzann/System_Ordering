<?php

namespace App\Helpers;

class SidebarRenderer
{
    public static function render(): void
    {
        $uri = $_SERVER['REQUEST_URI']; 
        $segments = explode('/', $uri);

        $role = null;
        $allowedRoles = ['customer', 'admin', 'spv'];

        $publicIndex = array_search('public', $segments);
        if ($publicIndex !== false && isset($segments[$publicIndex + 1])) {
            $candidate = $segments[$publicIndex + 1];
            if (in_array($candidate, $allowedRoles)) {
                $role = $candidate;
            }
        }

        if ($role) {
            $path = __DIR__ . "/../../Views/partials/{$role}/sidebar.php";
            if (file_exists($path)) {
                require_once $path;
            } else {
                echo "<!-- File sidebar.php untuk role '{$role}' tidak ditemukan -->";
            }
        } else {
            echo "<!-- Role tidak valid atau tidak terdeteksi dari URL -->";
        }
    }
}
