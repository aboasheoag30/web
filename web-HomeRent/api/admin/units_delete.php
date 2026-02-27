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
if($unitId<=0) json_error('unitId مطلوب',422);

$pdo=db();
$st=$pdo->prepare("SELECT u.id,u.property_id FROM units u JOIN properties p ON p.id=u.property_id WHERE u.id=? AND p.owner_id=? LIMIT 1");
$st->execute([$unitId,$ownerId]);
$u=$st->fetch();
if(!$u) json_error('الوحدة غير موجودة',404);

$st=$pdo->prepare("SELECT COUNT(*) AS c FROM contracts WHERE owner_id=? AND unit_id=?");
$st->execute([$ownerId,$unitId]);
$cnt=(int)($st->fetch()['c']??0);
if($cnt>0) json_error('لا يمكن حذف الوحدة لوجود عقد مرتبط بها',409);

$st=$pdo->prepare("DELETE FROM units WHERE id=?");
$st->execute([$unitId]);
json_ok(['unitId'=>$unitId]);
