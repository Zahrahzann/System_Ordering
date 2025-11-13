<?php
class View {
    /**
     * Render file view dari folder /views/
     * @param string $file - path relatif dari file view (tanpa .php)
     * @param array $data - data yang akan di-inject ke view
     */
    public static function render($file, $data = []) {
        $basePath = __DIR__ . '/../src/Views';
        $filePath = $basePath . '/' . $file . '.php';

        if (!file_exists($filePath)) {
            echo "<pre>View not found: {$filePath}</pre>";
            return;
        }

        extract($data); // Ubah array jadi variabel
        include $filePath;
    }
}
