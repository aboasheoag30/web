<?php
// register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// استدعاء ملف الاتصال والإعدادات من مجلد config
require_once __DIR__ . '/config/config.php';

// المعالجة الخلفية عند إرسال النموذج عبر AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'owner'; // الافتراضي مالك

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'الرجاء تعبئة الحقول الأساسية (الاسم، البريد، كلمة المرور)']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // التحقق أولاً من عدم تكرار البريد أو الهاتف
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR (phone = ? AND phone != '') LIMIT 1");
        $checkStmt->execute([$email, $phone]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'البريد الإلكتروني أو رقم الهاتف مسجل بالفعل!']);
            exit;
        }

        // إدخال المستخدم الجديد في جدول الـ users
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword, $role]);

        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقاً.']);
        exit;
    }
}

$appName = getAppSetting('app_name', 'HomeRent Pro');
$logoUrl = 'assets/logo.jpg';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد | <?php echo $appName; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { primary: '#2563eb', secondary: '#0f172a' }, 
                    fontFamily: { 'cairo': ['Cairo', 'sans-serif'] },
                } 
            } 
        }
    </script>
    <style>
        .login-gradient {
            background: radial-gradient(circle at top right, #eff6ff 0%, #f8fafc 100%);
        }
        .input-focus-effect:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
    </style>
</head>
<body class="login-gradient font-cairo min-h-screen flex items-center justify-center p-4">

    <div class="max-w-[480px] w-full bg-white/80 backdrop-blur-lg rounded-[2.5rem] shadow-[0_20px_50px_rgba(37,99,235,0.1)] p-8 md:p-10 border border-white my-6">
        
        <div class="text-center mb-6">
            <div class="relative inline-block mb-4">
                <?php if(file_exists(__DIR__ . '/' . $logoUrl)): ?>
                    <img src="<?php echo $logoUrl; ?>" alt="Logo" class="w-20 h-20 rounded-3xl object-cover shadow-lg transition-all duration-500 hover:scale-105">
                <?php else: ?>
                    <div class="w-20 h-20 bg-primary rounded-3xl rotate-12 flex items-center justify-center text-white text-4xl shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-user-plus -rotate-12"></i>
                    </div>
                <?php endif; ?>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-500 border-4 border-white rounded-full flex items-center justify-center text-[10px] text-white"><i class="fa-solid fa-plus"></i></div>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight"><?php echo $appName; ?></h1>
            <p class="text-slate-500 mt-1 font-semibold text-sm">انضم إلينا وابدأ إدارة عقاراتك بذكاء</p>
        </div>

        <form id="registerForm" class="space-y-4">
            <!-- نوع الحساب بطاقات راديو أنيقة -->
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mr-1">نوع الحساب</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="border-2 border-slate-200 rounded-2xl p-3 flex items-center gap-3 cursor-pointer hover:border-primary/50 transition-all has-[:checked]:border-primary has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="role" value="owner" checked class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="text-right">
                            <span class="block text-sm font-bold text-slate-700">مالك عقار</span>
                        </div>
                    </label>
                    <label class="border-2 border-slate-200 rounded-2xl p-3 flex items-center gap-3 cursor-pointer hover:border-primary/50 transition-all has-[:checked]:border-primary has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="role" value="tenant" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="text-right">
                            <span class="block text-sm font-bold text-slate-700">مستأجر</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- الاسم الكامل -->
            <div class="space-y-1">
                <label class="block text-xs font-black text-slate-500 mr-1">الاسم الكامل</label>
                <div class="relative group">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">
                        <i class="fa-solid fa-user text-base"></i>
                    </div>
                    <input type="text" name="name" required 
                           class="w-full bg-slate-100/50 border-2 border-slate-100 rounded-2xl py-3.5 pr-12 pl-4 focus:bg-white focus:border-primary outline-none transition-all font-bold text-slate-700 input-focus-effect" 
                           placeholder="محمد عبد الله">
                </div>
            </div>

            <!-- البريد الإلكتروني -->
            <div class="space-y-1">
                <label class="block text-xs font-black text-slate-500 mr-1">البريد الإلكتروني</label>
                <div class="relative group">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">
                        <i class="fa-solid fa-envelope text-base"></i>
                    </div>
                    <input type="email" name="email" required 
                           class="w-full bg-slate-100/50 border-2 border-slate-100 rounded-2xl py-3.5 pr-12 pl-4 focus:bg-white focus:border-primary outline-none transition-all font-bold text-slate-700 text-left input-focus-effect" 
                           placeholder="mail@example.com">
                </div>
            </div>

            <!-- رقم الجوال (اختياري) -->
            <div class="space-y-1">
                <label class="block text-xs font-black text-slate-500 mr-1">رقم الجوال (اختياري)</label>
                <div class="relative group">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">
                        <i class="fa-solid fa-mobile-screen-button text-base"></i>
                    </div>
                    <input type="tel" name="phone" 
                           class="w-full bg-slate-100/50 border-2 border-slate-100 rounded-2xl py-3.5 pr-12 pl-4 focus:bg-white focus:border-primary outline-none transition-all font-bold text-slate-700 text-left input-focus-effect" 
                           placeholder="05xxxxxxxx">
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="space-y-1">
                <label class="block text-xs font-black text-slate-500 mr-1">كلمة المرور</label>
                <div class="relative group">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">
                        <i class="fa-solid fa-lock text-base"></i>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="w-full bg-slate-100/50 border-2 border-slate-100 rounded-2xl py-3.5 pr-12 pl-12 focus:bg-white focus:border-primary outline-none transition-all font-bold text-slate-700 input-focus-effect" 
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword()" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-600 transition-colors">
                        <i id="toggleIcon" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- زر الإرسال -->
            <button type="submit" id="regBtn" class="w-full bg-secondary hover:bg-primary text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-100 transition-all duration-300 flex items-center justify-center gap-3 active:scale-[0.98] group relative mt-6">
                <span id="btnText" class="relative z-10">إنشاء الحساب الآن</span>
                <i class="fa-solid fa-chevron-left text-[10px] group-hover:-translate-x-1 transition-transform relative z-10"></i>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-slate-100 pt-4">
            <p class="text-slate-500 text-sm font-bold">لديك حساب بالفعل؟</p>
            <a href="login.php" class="inline-block mt-1 text-primary font-black text-sm hover:scale-105 transition-transform border-b-2 border-primary/20 pb-0.5 hover:border-primary">تسجيل الدخول للمنصة</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwdInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                pwdInput.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('regBtn');
            const btnText = document.getElementById('btnText');
            
            btn.disabled = true;
            btnText.innerText = 'جاري تسجيل حسابك...';
            btn.classList.add('opacity-80', 'cursor-not-allowed');

            try {
                const formData = new FormData(this);

                const response = await fetch('register.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast("������ تم إنشاء الحساب بنجاح! جاري تحويلك لوحدة الدخول...", "#10b981");
                    setTimeout(() => {
                        window.location.href = 'login.php'; 
                    }, 2000);
                } else {
                    throw new Error(data.error || "خطأ أثناء التسجيل");
                }

            } catch (error) {
                showToast("❌ " + error.message, "#ef4444");
                btn.disabled = false;
                btnText.innerText = 'إنشاء الحساب الآن';
                btn.classList.remove('opacity-80', 'cursor-not-allowed');
            }
        });

        function showToast(text, color) {
            Toastify({
                text: text,
                duration: 4000,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: { 
                    background: color, 
                    borderRadius: "20px", 
                    fontFamily: "'Cairo', sans-serif",
                    fontWeight: "800",
                    fontSize: "14px",
                    boxShadow: "0 10px 20px -5px rgba(0,0,0,0.1)"
                }
            }).showToast();
        }
    </script>
</body>
</html>