// HomeRent Web - Login
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // منع إعادة تحميل الصفحة

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const submitBtn = document.getElementById('submitBtn');
    const remember = document.getElementById('rememberMe')?.checked ?? false;

    // تغيير شكل الزر لوضع التحميل
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...';
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

    try {
        // الاتصال بالـ API الذي قمنا ببرمجته
        const response = await fetch('api/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        // التحقق من حالة الرد
        if (!response.ok) throw new Error(data.error || 'تعذر تسجيل الدخول');

        // إذا نجح الدخول: حفظ البيانات
        // إن لم يتم اختيار "تذكرني" نخزنها في sessionStorage
        const storage = remember ? localStorage : sessionStorage;
        storage.setItem('homerent_token', data.token);
        storage.setItem('homerent_user', JSON.stringify(data.user));

        // وللتوافق مع صفحات قديمة ما زالت تقرأ من localStorage
        localStorage.setItem('homerent_token', data.token);
        localStorage.setItem('homerent_user', JSON.stringify(data.user));

        // إظهار رسالة نجاح مؤقتة على الزر
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        submitBtn.classList.add('bg-green-600');
        submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> تم تسجيل الدخول بنجاح، جاري التحويل...';

        // توجيه المستخدم حسب الصلاحية (Role)
        setTimeout(() => {
            const role = data.user.role;
            // حالياً الواجهة المكتملة هي لوحة الإدارة (يمكن فصل لوحات Owner/Tenant لاحقاً)
            window.location.href = 'views/admin/dashboard.php';
        }, 1500);

    } catch (error) {
        // في حال حدوث خطأ
        if (window.hrToast) hrToast(error.message, 'error');
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span>تسجيل الدخول</span> <i class="fa-solid fa-arrow-right-to-bracket"></i>';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
});

// Toggle password visibility
document.getElementById('togglePassword')?.addEventListener('click', () => {
  const input = document.getElementById('password');
  if (!input) return;
  const isPass = input.type === 'password';
  input.type = isPass ? 'text' : 'password';
  const icon = document.querySelector('#togglePassword i');
  if (icon) icon.className = isPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
});