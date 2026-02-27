<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>ايجار ويب</title>
  <link rel="stylesheet" href="assets/site.css"/>
</head>
<body>
  <div class="wrap">
    <div class="h1">
      <div class="logo">EW</div>
      <div>
        <div class="title">ايجار ويب</div>
        <div class="small">منصة إدارة الإيجارات — دخول موحّد لجميع المستخدمين</div>
      </div>
    </div>

    <a class="cardlink" href="auth.php?tab=login">
      <b>تسجيل الدخول</b>
      <div class="small">مالك / موظف / مستأجر / مدير موقع</div>
    </a>

    <a class="cardlink" href="auth.php?tab=register">
      <b>إنشاء حساب مالك جديد</b>
      <div class="small">تسجيل حساب مالك عقار جديد</div>
    </a>

    <div class="footer">© <?= date('Y') ?> ايجار ويب</div>
  </div>
</body>
</html>
