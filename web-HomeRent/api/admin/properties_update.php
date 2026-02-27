<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');

$me=require_auth();
$ownerId=require_owner_scope($me);

$body=json_decode(file_get_contents('php://input'), true);
$propertyId=safe_int($body['propertyId']??0);
$name=safe_str($body['name']??'');
$city=safe_str($body['city']??'');
$district=safe_str($body['district']??'');

if($propertyId<=0 || $name==='') json_error('بيانات غير مكتملة',422);

$pdo=db();
$st=$pdo->prepare("UPDATE properties SET name=?, city=?, district=? WHERE id=? AND owner_id=?");
$st->execute([$name, $city!==''?$city:null, $district!==''?$district:null, $propertyId, $ownerId]);
if($st->rowCount()===0) json_error('العقار غير موجود',404);

json_ok(['propertyId'=>$propertyId]);
