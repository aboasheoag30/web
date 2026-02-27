<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');

$body=json_decode(file_get_contents('php://input'), true);
$name=safe_str($body['fullName'] ?? '');
$email=strtolower(safe_str($body['email'] ?? ''));
$phone=safe_str($body['phone'] ?? '');
$password=(string)($body['password'] ?? '');

if($name===''||$email===''||$password==='') json_error('البيانات غير مكتملة',422);
if(strlen($password) < 6) json_error('كلمة المرور قصيرة',422);

$pdo=db();
$st=$pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$st->execute([$email]);
if($st->fetch()) json_error('هذا البريد مستخدم مسبقاً',409);

$hash=password_hash($password, PASSWORD_BCRYPT);
$st=$pdo->prepare("INSERT INTO users (role,full_name,email,phone,password_hash) VALUES ('owner',?,?,?,?)");
$st->execute([$name,$email,$phone!==''?$phone:null,$hash]);
$uid=(int)$pdo->lastInsertId();

$token=jwt_sign(['uid'=>$uid,'role'=>'owner']);
json_ok(['token'=>$token,'user'=>['id'=>$uid,'role'=>'owner','ownerId'=>0,'fullName'=>$name,'email'=>$email,'phone'=>$phone]]);
