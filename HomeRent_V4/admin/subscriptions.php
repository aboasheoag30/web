<?php
// admin/subscriptions.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

// تعديل حالة الاشتراك (تفعيل / إلغاء)
if (isset($_GET['toggle_status']) && isset($_GET['sub_id'])) {
    $subId = (int)$_GET['sub_id'];
    $newStatus = $_GET['toggle_status'] === 'active' ? 'active' : 'expired';
    $pdo->prepare("UPDATE subscriptions SET status = ? WHERE id = ?")->execute([$newStatus, $subId]);
    header("Location: subscriptions.php"); exit;
}

// جلب الاشتراكات مع بيانات المستخدمين
$query = "SELECT s.*, u.name, u.email FROM subscriptions s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC";
$subscriptions = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الاشتراكات المدفوعة | إيجار ويب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <header class="bg-slate-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-black text-blue-400">������ التحكم باشتراكات المستخدمين</h1>
            <a href="dashboard.php" class="bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-xl text-sm font-bold transition">الرئيسية</a>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10">
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-500 text-xs font-black border-b">
                        <th class="p-4 pr-6">المشترك</th>
                        <th class="p-4">نوع الباقة</th>
                        <th class="p-4">تاريخ الانتهاء</th>
                        <th class="p-4">الحالة</th>
                        <th class="p-4 pl-6 text-left">إجراء سريع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold">
                    <?php if(empty($subscriptions)): ?>
                        <tr><td colspan="5" class="p-8 text-center text-slate-400 italic">لا توجد اشتراكات مسجلة حالياً.</td></tr>
                    <?php endif; ?>
                    <?php foreach($subscriptions as $s): ?>
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-4 pr-6">
                            <div class="font-bold text-slate-900"><?php echo htmlspecialchars($s['name']); ?></div>
                            <div class="text-xs text-slate-400 font-normal"><?php echo htmlspecialchars($s['email']); ?></div>
                        </td>
                        <td class="p-4 text-slate-700"><?php echo htmlspecialchars($s['package_name'] ?? 'الباقة القياسية'); ?></td>
                        <td class="p-4 font-mono text-xs"><?php echo $s['ends_at'] ?? 'غير محدد'; ?></td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo $s['status'] === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'; ?>">
                                <?php echo $s['status'] === 'active' ? 'نشط' : 'منتهي / معلق'; ?>
                            </span>
                        </td>
                        <td class="p-4 pl-6 text-left">
                            <?php if($s['status'] === 'active'): ?>
                                <a href="subscriptions.php?toggle_status=expired&sub_id=<?php echo $s['id']; ?>" class="text-xs bg-amber-50 text-amber-600 px-3 py-1.5 rounded-xl font-bold transition">تعطيل باقته</a>
                            <?php else: ?>
                                <a href="subscriptions.php?toggle_status=active&sub_id=<?php echo $s['id']; ?>" class="text-xs bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl font-bold transition">تفعيل الآن</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>