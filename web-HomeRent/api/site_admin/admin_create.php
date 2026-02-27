<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/mailer.php';
cors(); require_method('POST');
$me=require_auth();
if(($me['role']??'')!=='site_admin') json_error('Forbidden',403);

$body=json_decode(file_get_contents('php://input'), true);
$name=safe_str($body['fullName']??'');
$email=strtolower(safe_str($body['email']??''));
$phone=safe_str($body['phone']??'');
if($name===''||$email==='') json_error('البيانات غير مكتملة',422);

$pdo=db();
$st=$pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$st->execute([$email]);
if($st->fetch()) json_error('هذا البريد مستخدم مسبقاً',409);

$pw=random_password(10);
$hash=password_hash($pw, PASSWORD_BCRYPT);
$st=$pdo->prepare("INSERT INTO users (role,full_name,email,phone,password_hash) VALUES ('site_admin_staff',?,?,?,?)");
$st->execute([$name,$email,$phone!==''?$phone:null,$hash]);

send_mail_simple($email,'بيانات دخول مسؤول نظام - '.APP_NAME,"
<div dir='rtl' style='font-family:Tahoma,Arial;line-height:1.8'>
<h3>تمت إضافتك كـ مسؤول نظام (صلاحيات محدودة) في ".APP_NAME."</h3>
<p><b>البريد:</b> {$email}<br/><b>كلمة المرور:</b> {$pw}</p>
<p>الدخول: <a href='".BASE_URL."/web/login.html'>".BASE_URL."/web/login.html</a></p>
</div>");

json_ok(['adminStaffId'=>(int)$pdo->lastInsertId()]);
