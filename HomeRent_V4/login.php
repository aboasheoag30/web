<?php
// 1. تفعيل عرض الأخطاء مؤقتاً لتتبع المشاكل أثناء التطوير (يمكنك إغلاقها عند رفع الموقع)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// 2. تضمين ملف الاتصال والإعدادات
require_once 'config.php'; 

$error_message = '';

// 3. معالجة بيانات نموذج تسجيل الدخول عند الإرسال (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // استقبال مدخلات المستخدم وتنظيفها
    $emailInput = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password   = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($emailInput) || empty($password)) {
        $error_message = "������ يرجى كتابة البريد الإلكتروني وكلمة المرور.";
    } else {
        try {
            // الاستعلام عن المستخدم بواسطة البريد الإلكتروني
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $emailInput]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // التحقق من وجود المستخدم، ومن ثم مطابقة كلمة المرور مع الحقل الصحيح password_hash
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // حماية الجلسة وتجديد المعرف لمنع الاختراق
                session_regenerate_id(true);
                
                // تخزين البيانات الأساسية في الـ Session بناءً على أسماء الحقول الحقيقية لديك
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role']  = $user['role'] ?? 'user'; // يقرأ الحقل role مباشرة (مثل owner, tenant, admin)
                
                // التوجيه التلقائي الذكي حسب رتبة المستخدم
                if (in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;

            } else {
                // رسالة موحدة ومؤمنة لعدم كشف هل الخطأ في الإيميل أو الباسورد
                $error_message = "❌ البريد الإلكتروني أو كلمة المرور غير صحيحة.";
            }

        } catch (PDOException $e) {
            $error_message = "⚙️ خطأ في النظام: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - HomeRent Plus</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #666; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 16px; text-align: left; direction: ltr; }
        button { width: 100%; padding: 12px; background-color: #28a745; border: none; border-radius: 4px; color: white; font-size: 16px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #218838; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; line-height: 1.5; text-align: right; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>تسجيل الدخول - HomeRent</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" placeholder="example@domain.com" required autocomplete="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>

        <button type="submit">تسجيل الدخول</button>
    </form>
</div>

</body>
</html>