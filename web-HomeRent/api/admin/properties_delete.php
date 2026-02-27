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
if($propertyId<=0) json_error('propertyId مطلوب',422);

$pdo=db();

$st=$pdo->prepare("SELECT id FROM properties WHERE id=? AND owner_id=? LIMIT 1");
$st->execute([$propertyId,$ownerId]);
if(!$st->fetch()) json_error('العقار غير موجود',404);

$st=$pdo->prepare("SELECT COUNT(*) AS c FROM contracts WHERE owner_id=? AND property_id=?");
$st->execute([$ownerId,$propertyId]);
$cnt=(int)($st->fetch()['c']??0);
if($cnt>0) json_error('لا يمكن حذف العقار لوجود عقود مرتبطة به',409);

$pdo->beginTransaction();
try{
  $st=$pdo->prepare("DELETE FROM units WHERE property_id=?");
  $st->execute([$propertyId]);
  $st=$pdo->prepare("DELETE FROM properties WHERE id=? AND owner_id=?");
  $st->execute([$propertyId,$ownerId]);
  $pdo->commit();
  json_ok(['propertyId'=>$propertyId]);
}catch(Throwable $e){
  $pdo->rollBack();
  json_error('فشل حذف العقار',500,['error'=>$e->getMessage()]);
}
