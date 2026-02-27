<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');
$me=require_auth();
if(!in_array(($me['role']??''), ['site_admin','site_admin_staff'], true)) json_error('Forbidden',403);
$body=json_decode(file_get_contents('php://input'), true);
$userId=safe_int($body['userId']??0);
$status=safe_str($body['status']??'');
if($userId<=0||!in_array($status,['active','disabled'],true)) json_error('بيانات غير صحيحة',422);
$pdo=db();

$st=$pdo->prepare("SELECT role FROM users WHERE id=? LIMIT 1");
$st->execute([$userId]);
$row=$st->fetch();
if(!$row) json_error('المستخدم غير موجود',404);
if($row['role']==='site_admin') json_error('لا يمكن تعديل حساب مدير الموقع',403);

$st=$pdo->prepare("UPDATE users SET status=? WHERE id=? AND role <> 'site_admin'");
$st->execute([$status,$userId]);
json_ok();
