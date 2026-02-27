<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('POST');

$me=require_auth();
$data=read_json();
$old=trim((string)($data['oldPassword'] ?? ''));
$new=(string)($data['newPassword'] ?? '');

if(strlen($new) < 8) json_error('كلمة المرور الجديدة قصيرة',400);
if($old==='') json_error('كلمة المرور الحالية مطلوبة',400);

$pdo=db();
$st=$pdo->prepare("SELECT password_hash FROM users WHERE id=? LIMIT 1");
$st->execute([(int)$me['id']]);
$row=$st->fetch();
if(!$row) json_error('User not found',404);
if(!password_verify($old, (string)$row['password_hash'])) json_error('كلمة المرور الحالية غير صحيحة',400);

$hash=password_hash($new, PASSWORD_BCRYPT);
$st=$pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
$st->execute([$hash, (int)$me['id']]);

json_ok(['message'=>'Password changed']);
