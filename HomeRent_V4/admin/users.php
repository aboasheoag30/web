<?php
// admin/users.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$id]);
    header("Location: users.php"); exit;
}

$users = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الأعضاء | إيجار ويب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <header class="bg-slate-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-black text-blue-400"><i class="fa-solid fa-users-gear ml-2"></i>التحكم بحسابات الأعضاء</h1>
            <a href="dashboard.php" class="bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-xl text-sm font-bold transition">الرئيسية</a>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10">
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-500 text-xs font-black border-b">
                        <th class="p-4 pr-6">الاسم الكامل</th>
                        <th class="p-4">البريد الإلكتروني</th>
                        <th class="p-4">رقم الجوال</th>
                        <th class="p-4">نوع الحساب</th>
                        <th class="p-4 pl-6 text-left">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold">
                    <?php foreach($users as $u): ?>
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-4 pr-6 font-bold text-slate-900"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td class="p-4 font-mono text-xs"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($u['phone'] ?? '---'); ?></td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold <?php echo $u['role'] === 'owner' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'; ?>">
                                <?php echo $u['role'] === 'owner' ? 'مالك عقار' : 'مستأجر'; ?>
                            </span>
                        </td>
                        <td class="p-4 pl-6 text-left">
                            <a href="users.php?delete=<?php echo $u['id']; ?>" onclick="return confirm('هل تريد إزالة الحساب نهائياً؟')" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-2 rounded-xl font-bold transition">حذف الحساب</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>