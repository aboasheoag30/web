<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');
$me=require_auth(); $ownerId=require_owner_scope($me);
$body=json_decode(file_get_contents('php://input'), true);
$name=safe_str($body['name']??''); $city=safe_str($body['city']??''); $district=safe_str($body['district']??'');
if($name==='') json_error('اسم العقار مطلوب',422);
$pdo=db();
$st=$pdo->prepare("INSERT INTO properties (owner_id,name,city,district) VALUES (?,?,?,?)");
$st->execute([$ownerId,$name,$city!==''?$city:null,$district!==''?$district:null]);
json_ok(['propertyId'=>(int)$pdo->lastInsertId()]);
