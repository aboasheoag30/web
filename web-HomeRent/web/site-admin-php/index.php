<?php
declare(strict_types=1);

$page = $_GET['page'] ?? 'dashboard';
$allowed = ['dashboard','users','user_edit','messages','message_send'];
if (!in_array($page, $allowed, true)) $page = 'dashboard';

require_once __DIR__ . '/_auth.php'; // حماية/جلسة لاحقًا

$titleMap = [
  'dashboard'=>'الرئيسية',
  'users'=>'المستخدمون',
  'user_edit'=>'تعديل مستخدم',
  'messages'=>'الرسائل المرسلة',
  'message_send'=>'إرسال رسالة',
];
$title = $titleMap[$page] ?? 'لوحة التحكم';

ob_start();
require __DIR__ . "/pages/$page.php";
$contentBody = ob_get_clean();

$active = match($page){
  'dashboard'=>'dashboard',
  'users','user_edit'=>'users',
  'messages'=>'messages',
  'message_send'=>'send',
  default=>'dashboard'
};

$content = '<div class="topbar"><div class="h1">'.htmlspecialchars($title).'</div></div>'.$contentBody;

require __DIR__ . '/_layout.php';