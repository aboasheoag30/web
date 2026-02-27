<?php
// $title, $active, $content (html) must be provided
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="assets/admin.css"/>
</head>
<body>
<div class="wrap">
  <aside class="sidebar">
    <div class="brand">
      <div class="logo">EW</div>
      <div>
        <div style="font-weight:900">ايجار ويب</div>
        <div class="small" style="color:rgba(255,255,255,.75)">لوحة مدير الموقع</div>
      </div>
    </div>

    <nav class="nav">
      <a class="<?= $active==='dashboard'?'active':'' ?>" href="index.php?page=dashboard">الرئيسية</a>
      <a class="<?= $active==='users'?'active':'' ?>" href="index.php?page=users">المستخدمون</a>
      <a class="<?= $active==='messages'?'active':'' ?>" href="index.php?page=messages">الرسائل المرسلة</a>
      <a class="<?= $active==='send'?'active':'' ?>" href="index.php?page=message_send">إرسال رسالة</a>
      <a href="../login.html" onclick="localStorage.removeItem('ijarweb_token')">تسجيل خروج</a>
    </nav>
  </aside>

  <main class="content">
    <?= $content ?>
  </main>
</div>
</body>
</html>