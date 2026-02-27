<?php
// منع الوصول المباشر للملف
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    exit('Not Allowed');
}

// إعدادات قاعدة البيانات الخاصة بسيرفر QNAP
define('DB_HOST', '127.0.0.1');      // تم مسح المنفذ والمسافة الزائدة
define('DB_NAME', 'homerent2_db');   // اسم قاعدة البيانات
define('DB_USER', 'web-user');       // اسم مستخدم قاعدة البيانات

// وضعنا الباسورد الصحيح وحذفنا السطر المكرر الذي كان يجعله فارغاً
define('DB_PASS', '664422$$');               

// مفتاح التشفير السري
define('JWT_SECRET', 'H0meR3nt_Secr3t_K3y_2026!@#_QnAp');