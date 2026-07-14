<?php
// admin/settings.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([trim($value), $key]);
    }
    $successMessage = 'تم تحديث بيانات وموقع تطبيق إيجار ويب بنجاح!';
}

$appName = getAppSetting('app_name', 'إيجار ويب');
$footerText = getAppSetting('app_footer', 'Homerent Plus © 2026 جميع الحقوق محفوظة.');
$contactEmail = getAppSetting('contact_email', 'support@ejarweb.com');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعدادات تطبيق إيجار ويب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <header class="bg-slate-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-black text-blue-400">⚙️ بيانات وإعدادات المنصة</h1>
            <a href="dashboard.php" class="bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-xl text-sm font-bold transition">الرئيسية</a>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10 max-w-xl">
        <?php if(!empty($successMessage)): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 p-4 rounded-2xl font-bold mb-6 text-center">
                ������ <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <form action="settings.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم التطبيق / الموقع</label>
                    <input type="text" name="settings[app_name]" value="<?php echo htmlspecialchars($appName); ?>" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 font-bold text-slate-700 focus:bg-white focus:border-primary outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">بريد الدعم الفني للموقع</label>
                    <input type="email" name="settings[contact_email]" value="<?php echo htmlspecialchars($contactEmail); ?>" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 font-bold text-slate-700 focus:bg-white focus:border-primary outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">تذييل الموقع وحقوق الحفظ</label>
                    <input type="text" name="settings[app_footer]" value="<?php echo htmlspecialchars($footerText); ?>" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 font-bold text-slate-700 focus:bg-white focus:border-primary outline-none transition-all">
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black py-4 rounded-xl transition shadow-lg">
                    حفظ ونشر البيانات الحالية
                </button>
            </form>
        </div>
    </main>
</body>
</html>