<?php
require_once __DIR__ . '/config.php';

function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
$dsn = "mysql:host=" . DB_HOST . ";port=3307;dbname=" . DB_NAME . ";charset=utf8mb4";        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // هذا السطر سيكشف لنا سبب رفض قاعدة البيانات
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['error' => 'سبب الرفض: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}