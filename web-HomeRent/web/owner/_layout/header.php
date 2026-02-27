<?php // owner/_layout/header.php ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars(page_title($title)) ?></title>
  <link rel="icon" href="../assets/logo.png"/>
  <link rel="apple-touch-icon" href="../assets/logo.png"/>

  <link rel="stylesheet" href="../assets/official.css"/>
  <link rel="stylesheet" href="../assets/owner.css"/>
</head>
<body>
<div class="layout">

<header class="topbar">
  <div class="container topbar-inner">
    <div class="brand">
      <div class="logo"><img src="../assets/logo.png" alt="إيجار ويب" onerror="this.style.display='none';this.parentElement.textContent='EW';"></div>
      <div>
        <div class="brand-title">إيجار ويب</div>
        <div class="brand-sub"><?= htmlspecialchars($title) ?></div>
      </div>
    </div>

    <div class="top-actions">
      <button class="icon-btn" id="openDrawerBtn" title="القائمة">☰</button>
      <button class="icon-btn primary" onclick="location.href='../common/change_password.html'">تغيير كلمة المرور</button>
      <button class="icon-btn danger" onclick="logout()">خروج</button>
    </div>
  </div>
</header>

<div id="drawerOverlay" class="drawer-overlay"></div>
<div id="drawer" class="drawer">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px">
    <div style="color:#fff;font-weight:900">القائمة</div>
    <button class="icon-btn" id="closeDrawerBtn">إغلاق</button>
  </div>
