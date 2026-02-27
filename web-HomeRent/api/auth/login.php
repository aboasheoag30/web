<?php
// تفعيل إظهار الأخطاء برمجياً لاكتشاف المشكلة
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'طريقة الطلب غير مسموح بها.']);
    exit;
}

try {
    // استدعاء الملفات (هنا قد يكون الخطأ المخفي)
    require_once __DIR__ . '/../core/db.php';
    require_once __DIR__ . '/../core/auth.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $email = filter_var(strtolower(trim($input['email'] ?? '')), FILTER_SANITIZE_EMAIL);
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'البيانات غير مكتملة، يرجى إدخال البريد وكلمة المرور.']);
        exit;
    }

    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT id, role, owner_id, full_name, email, password_hash, status FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.']);
        exit;
    }

    if ($user['status'] !== 'active') {
        http_response_code(403);
        echo json_encode(['error' => 'هذا الحساب محظور أو غير مفعل. يرجى التواصل مع الإدارة.']);
        exit;
    }

    $token = generate_jwt([
        'uid' => $user['id'],
        'role' => $user['role'],
        'owner_id' => $user['owner_id']
    ]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'تم تسجيل الدخول بنجاح',
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'name' => $user['full_name'],
            'role' => $user['role'],
            'email' => $user['email']
        ]
    ]);

// هنا السحر: صيد جميع أنواع الأخطاء وطباعتها
} catch (\Throwable $e) { 
    http_response_code(500);
    echo json_encode([
        'error' => 'تم اكتشاف خطأ برمجي!',
        'details' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}