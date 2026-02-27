<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');

$body=json_decode(file_get_contents('php://input'), true);
$email=strtolower(safe_str($body['email'] ?? ''));
$password=(string)($body['password'] ?? '');
if($email==='' || $password==='') json_error('البيانات غير مكتملة',422);

$pdo=db();
$st=$pdo->prepare("SELECT id,role,owner_id,full_name,email,phone,password_hash,status FROM users WHERE email=? LIMIT 1");
$st->execute([$email]);
$u=$st->fetch();
if(!$u || $u['status']!=='active') json_error('بيانات الدخول غير صحيحة',401);
if(!password_verify($password,$u['password_hash'])) json_error('بيانات الدخول غير صحيحة',401);

$payload=['uid'=>(int)$u['id'],'role'=>$u['role']];
if($u['role']==='staff') $payload['owner_id']=(int)($u['owner_id']??0);
$token=jwt_sign($payload);

json_ok(['token'=>$token,'user'=>[
  'id'=>(int)$u['id'],'role'=>$u['role'],'ownerId'=>(int)($u['owner_id']??0),
  'fullName'=>$u['full_name'],'email'=>$u['email'],'phone'=>$u['phone']
]]);
