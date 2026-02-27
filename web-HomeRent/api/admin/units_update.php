<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');

$me=require_auth();
$ownerId=require_owner_scope($me);

$body=json_decode(file_get_contents('php://input'), true);
$unitId=safe_int($body['unitId']??0);
$unitType=safe_str($body['unitType']??'شقة');
$name=safe_str($body['name']??'');
$rentAmount=(float)($body['rentAmount']??0);
$status=safe_str($body['status']??'available');
if($unitId<=0 || $name==='') json_error('بيانات غير مكتملة',422);
if(!in_array($status,['available','rented'],true)) $status='available';

$pdo=db();
$st=$pdo->prepare("SELECT u.id FROM units u JOIN properties p ON p.id=u.property_id WHERE u.id=? AND p.owner_id=? LIMIT 1");
$st->execute([$unitId,$ownerId]);
if(!$st->fetch()) json_error('الوحدة غير موجودة',404);

$st=$pdo->prepare("UPDATE units SET unit_type=?, name=?, rent_amount=?, status=? WHERE id=?");
$st->execute([$unitType,$name,$rentAmount,$status,$unitId]);

json_ok(['unitId'=>$unitId]);
