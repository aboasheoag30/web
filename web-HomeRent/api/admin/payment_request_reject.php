<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('POST');
$me=require_auth(); $ownerId=require_owner_scope($me);
$body=json_decode(file_get_contents('php://input'), true);
$requestId=safe_int($body['requestId']??0);
$notes=safe_str($body['notes']??'');
if($requestId<=0) json_error('requestId مطلوب',422);

$pdo=db(); $pdo->beginTransaction();
try{
  $st=$pdo->prepare("SELECT pr.id,pr.schedule_id,pr.status,c.owner_id
                     FROM payment_requests pr JOIN contracts c ON c.id=pr.contract_id
                     WHERE pr.id=? LIMIT 1");
  $st->execute([$requestId]);
  $r=$st->fetch();
  if(!$r) json_error('الطلب غير موجود',404);
  if((int)$r['owner_id']!==$ownerId) json_error('Forbidden',403);
  if($r['status']!=='pending') json_error('لا يمكن رفض هذا الطلب',422);

  $st=$pdo->prepare("UPDATE payment_requests SET status='rejected', notes=? WHERE id=?");
  $st->execute([$notes!==''?$notes:null,$requestId]);

  $st=$pdo->prepare("UPDATE contract_schedules SET status='unpaid' WHERE id=? AND status='pending'");
  $st->execute([(int)$r['schedule_id']]);

  $pdo->commit();
  json_ok();
}catch(Throwable $e){
  $pdo->rollBack();
  json_error('فشل رفض الطلب',500,['error'=>$e->getMessage()]);
}
