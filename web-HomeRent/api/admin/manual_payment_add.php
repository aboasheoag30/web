<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/config.php';
cors(); require_method('POST');
$me=require_auth(); $ownerId=require_owner_scope($me);
$body=json_decode(file_get_contents('php://input'), true);
$scheduleId=safe_int($body['scheduleId']??0);
$amount=(float)($body['amount']??0);
if($scheduleId<=0) json_error('scheduleId مطلوب',422);
if($amount<=0) json_error('amount مطلوب',422);

$pdo=db(); $pdo->beginTransaction();
try{
  $st=$pdo->prepare("SELECT cs.id, c.id AS contract_id, c.tenant_id, c.owner_id
                     FROM contract_schedules cs JOIN contracts c ON c.id=cs.contract_id
                     WHERE cs.id=? LIMIT 1");
  $st->execute([$scheduleId]);
  $s=$st->fetch();
  if(!$s) json_error('الاستحقاق غير موجود',404);
  if((int)$s['owner_id']!==$ownerId) json_error('Forbidden',403);

  $st=$pdo->prepare("INSERT INTO payments (contract_id,schedule_id,tenant_id,amount,source) VALUES (?,?,?,?, 'manual')");
  $st->execute([(int)$s['contract_id'],$scheduleId,(int)$s['tenant_id'],$amount]);
  $paymentId=(int)$pdo->lastInsertId();

  $st=$pdo->prepare("UPDATE contract_schedules SET status='paid', paid_at=NOW() WHERE id=?");
  $st->execute([$scheduleId]);

  $pdo->commit();
  $receiptUrl = BASE_URL . "/web/receipt.php?paymentId=" . $paymentId;
  json_ok(['paymentId'=>$paymentId,'receiptUrl'=>$receiptUrl]);
}catch(Throwable $e){
  $pdo->rollBack();
  json_error('فشل إضافة سداد',500,['error'=>$e->getMessage()]);
}
