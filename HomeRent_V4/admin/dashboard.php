<?php
// admin/dashboard.php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// جدار الحماية الأمني: يفحص رتبة المسؤول (admin أو super_admin) بناءً على مفتاح الجلسة المعتمد في الـ login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    header("Location: ../login.php"); 
    exit;
}

// المسار الصحيح لاستدعاء ملف الاتصال والإعدادات (الخروج من مجلد admin خطوة للخلف)
require_once __DIR__ . '/../config.php';

// قراءة اسم التطبيق مباشرة من المصفوفة المستخرجة في ملف config
$appName = isset($app_settings['app_name']) ? $app_settings['app_name'] : 'إيجار ويب';
$logoUrl = '../assets/logo.jpg';

try {
    // 1. إجمالي الملاك والمستأجرين من جدول users
    $totalOwners = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'owner'")->fetchColumn() ?? 0;
    $totalTenants = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tenant'")->fetchColumn() ?? 0;
    
    // 2. إجمالي الاشتراكات النشطة في التطبيق
    $activeSubs = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'")->fetchColumn() ?? 0;
    
    // 3. إجمالي دخل الاشتراكات
    $totalRevenue = $pdo->query("SELECT SUM(amount) FROM subscription_payments WHERE status = 'paid'")->fetchColumn() ?? 0;

    // 4. آخر الحسابات المسجلة مع استثناء المسؤولين من القائمة
    $recentUsers = $pdo->query("SELECT id, full_name, email, role FROM users WHERE role NOT IN ('admin', 'super_admin') ORDER BY id DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $totalOwners = $totalTenants = $activeSubs = $totalRevenue = 0;
    $recentUsers = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة العامة | <?php echo htmlspecialchars($appName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#2563eb', secondary: '#0f172a' }, fontFamily: { 'cairo': ['Cairo', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-slate-50 font-cairo text-slate-800 min-h-screen flex flex-col">

    <!-- Navbar العلوي -->
    <header class="bg-secondary text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-reverse space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center font-bold text-white text-lg">
                    <i class="fa-solid fa-toolbox"></i>
                </div>
                <div>
                    <span class="text-lg font-black tracking-wide text-blue-400">لوحة التحكم الإدارية</span>
                    <span class="block text-xs text-slate-400"><?php echo htmlspecialchars($appName); ?></span>
                </div>
            </div>
            
            <nav class="hidden md:flex items-center gap-6 font-bold text-sm text-slate-300">
                <a href="dashboard.php" class="text-white border-b-2 border-blue-500 pb-1">الرئيسية</a>
                <a href="users.php" class="hover:text-white transition">المستخدمين</a>
                <a href="subscriptions.php" class="hover:text-white transition">الاشتراكات</a>
                <a href="settings.php" class="hover:text-white transition">إعدادات التطبيق</a>
            </nav>

            <a href="../login.php?action=logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-power-off"></i> <span>خروج</span>
            </a>
        </div>
    </header>

    <!-- المحتوى الرئيسي -->
    <main class="container mx-auto px-6 py-10 flex-grow">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900">أهلاً بك في منصة الإدارة ⚙️</h1>
            <p class="text-slate-500 mt-1 font-medium">متابعة حسابات المنصة، تفعيل باقات الاشتراكات، وضبط إعدادات تطبيق إيجار ويب.</p>
        </div>

        <!-- كروت الإحصائيات -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-slate-400 text-xs font-black">عدد الملاك</span>
                    <span class="block text-3xl font-black text-slate-900 mt-1"><?php echo $totalOwners; ?></span>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-primary rounded-2xl flex items-center justify-center text-xl"><i class="fa-solid fa-user-tie"></i></div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-slate-400 text-xs font-black">عدد المستأجرين</span>
                    <span class="block text-3xl font-black text-slate-900 mt-1"><?php echo $totalTenants; ?></span>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl"><i class="fa-solid fa-users"></i></div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-slate-400 text-xs font-black">الاشتراكات الفعالة</span>
                    <span class="block text-3xl font-black text-slate-900 mt-1"><?php echo $activeSubs; ?></span>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl"><i class="fa-solid fa-credit-card"></i></div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="block text-slate-400 text-xs font-black">إيرادات التطبيق</span>
                    <span class="block text-2xl font-black text-emerald-600 mt-1"><?php echo number_format($totalRevenue, 2); ?> ر.س</span>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl"><i class="fa-solid fa-wallet"></i></div>
            </div>
        </div>

        <!-- آخر المسجلين -->
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 font-black text-slate-900">المسجلين حديثاً في التطبيق</div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 text-xs font-black border-b">
                            <th class="p-4 pr-6">الاسم</th>
                            <th class="p-4">البريد الالكتروني</th>
                            <th class="p-4">نوع الحساب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-700">
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach($recentUsers as $user): ?>
                                <tr>
                                    <td class="p-4 pr-6 font-bold text-slate-900"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td class="p-4 font-mono text-xs"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo $user['role'] === 'owner' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'; ?>">
                                            <?php echo $user['role'] === 'owner' ? 'مالك عقار' : 'مستأجر'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="p-6 text-center text-slate-400 font-medium">لا يوجد مستخدمين مسجلين حالياً.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>