<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('GET');

$me=require_auth();
$ownerId=require_owner_scope($me);
$contractId = safe_int($_GET['id'] ?? 0);
if($contractId<=0) json_error('id required',400);

$pdo=db();

function fetch_contract(PDO $pdo, int $contractId, int $ownerId): array {
  // محاولة قراءة tenant_identity إن كان العمود موجود
  $sqlWithIdentity = "
SELECT c.id,c.contract_number,c.start_date,c.months,c.payment_plan,c.total_amount,c.status,
  c.tenant_identity,
  DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) AS end_date,
  DATEDIFF(DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY), CURDATE()) AS days_to_end,
  t.full_name AS tenant_name, t.email AS tenant_email, t.phone AS tenant_phone,
  p.name AS property_name, p.city, p.district,
  u.unit_type, u.name AS unit_name, u.rent_amount
FROM contracts c
JOIN users t ON t.id=c.tenant_id
JOIN properties p ON p.id=c.property_id
JOIN units u ON u.id=c.unit_id
WHERE c.id=? AND c.owner_id=? LIMIT 1
";
  $sqlNoIdentity = "
SELECT c.id,c.contract_number,c.start_date,c.months,c.payment_plan,c.total_amount,c.status,
  DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) AS end_date,
  DATEDIFF(DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY), CURDATE()) AS days_to_end,
  t.full_name AS tenant_name, t.email AS tenant_email, t.phone AS tenant_phone,
  p.name AS property_name, p.city, p.district,
  u.unit_type, u.name AS unit_name, u.rent_amount
FROM contracts c
JOIN users t ON t.id=c.tenant_id
JOIN properties p ON p.id=c.property_id
JOIN units u ON u.id=c.unit_id
WHERE c.id=? AND c.owner_id=? LIMIT 1
";
  try{
    $st=$pdo->prepare($sqlWithIdentity);
    $st->execute([$contractId,$ownerId]);
    $row=$st->fetch();
    if($row) return $row;
  }catch(Throwable $e){
    // ignore, fallback below
  }
  $st=$pdo->prepare($sqlNoIdentity);
  $st->execute([$contractId,$ownerId]);
  $row=$st->fetch();
  return $row ?: [];
}

$c = fetch_contract($pdo, $contractId, $ownerId);
if(!$c) json_error('Not found',404);

$st=$pdo->prepare("SELECT id,seq,due_date,amount,status,paid_at FROM contract_schedules WHERE contract_id=? ORDER BY seq ASC");
$st->execute([$contractId]);
$schedules=$st->fetchAll();

$st=$pdo->prepare("SELECT id,amount,paid_at,source FROM payments WHERE contract_id=? ORDER BY id DESC");
$st->execute([$contractId]);
$payments=$st->fetchAll();

$tot=0.0;$paid=0.0;$over=0.0;$due30=0.0;$future=0.0;
$today=new DateTime('today');
$in30=(clone $today)->modify('+30 day');
foreach($schedules as $it){
  $amt=(float)$it['amount']; $tot+=$amt;
  if(($it['status']??'')==='unpaid'){
    $dd=new DateTime((string)$it['due_date']);
    if($dd < $today) $over += $amt;
    else if($dd <= $in30) $due30 += $amt;
    else $future += $amt;
  }
}
foreach($payments as $pmt){ $paid += (float)$pmt['amount']; }
$remaining=max(0.0,$tot-$paid);

json_ok(['contract'=>$c,'schedules'=>$schedules,'payments'=>$payments,'summary'=>[
  'total'=>$tot,'paid'=>$paid,'remaining'=>$remaining,'overdue'=>$over,'due30'=>$due30,'future'=>$future
]]);
