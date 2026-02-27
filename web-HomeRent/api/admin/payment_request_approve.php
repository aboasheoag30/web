<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/config.php';
cors(); require_method('POST');
$me=require_auth(); $ownerId=require_owner_scope($me);
$body=json_decode(file_get_contents('php://input'), true);
$requestId=safe_int($body['requestId']??0);
if($requestId<=0) json_error('requestId مطلوب',422);

$pdo=db(); $pdo->beginTransaction();
try{
  $st=$pdo->prepare("SELECT pr.id,pr.contract_id,pr.schedule_id,pr.tenant_id,pr.status,c.owner_id,cs.amount
                     FROM payment_requests pr
                     JOIN contracts c ON c.id=pr.contract_id
                     JOIN contract_schedules cs ON cs.id=pr.schedule_id
                     WHERE pr.id=? LIMIT 1");
  $st->execute([$requestId]);
  $r=$st->fetch();
  if(!$r) json_error('الطلب غير موجود',404);
  if((int)$r['owner_id']!==$ownerId) json_error('Forbidden',403);
  if($r['status']!=='pending') json_error('لا يمكن اعتماد هذا الطلب',422);

  $amount=(float)$r['amount'];
  $st=$pdo->prepare("INSERT INTO payments (contract_id,schedule_id,tenant_id,amount,source) VALUES (?,?,?,?, 'request_approved')");
  $st->execute([(int)$r['contract_id'],(int)$r['schedule_id'],(int)$r['tenant_id'],$amount]);
  $paymentId=(int)$pdo->lastInsertId();

  $st=$pdo->prepare("UPDATE contract_schedules SET status='paid', paid_at=NOW() WHERE id=?");
  $st->execute([(int)$r['schedule_id']]);

  $st=$pdo->prepare("UPDATE payment_requests SET status='approved' WHERE id=?");
  $st->execute([$requestId]);

  $pdo->commit();
  $receiptUrl = BASE_URL . "/web/receipt.php?paymentId=" . $paymentId;
  json_ok(['paymentId'=>$paymentId,'receiptUrl'=>$receiptUrl]);
}catch(Throwable $e){
  $pdo->rollBack();
  json_error('فشل اعتماد الطلب',500,['error'=>$e->getMessage()]);
}
