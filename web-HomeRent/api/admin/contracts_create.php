<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/mailer.php';
cors(); require_method('POST');

$me=require_auth();
$ownerId=require_owner_scope($me);

function add_months_keep_day(DateTime $dt, int $months): DateTime {
  $base = clone $dt;
  $day = (int)$base->format('j');
  // Move to first day to avoid overflow then set day to min(last day, original day)
  $base->modify('first day of this month');
  $base->modify('+' . $months . ' month');
  $lastDay = (int)$base->format('t');
  $base->setDate((int)$base->format('Y'), (int)$base->format('n'), min($day, $lastDay));
  return $base;
}

function build_schedule(PDO $pdo, int $contractId, string $startDate, int $months, string $plan, float $totalAmount): array {
  $allowed = ['one','monthly','two','three','four'];
  if (!in_array($plan, $allowed, true)) throw new RuntimeException('Bad plan');

  $dtStart = new DateTime($startDate);

  // offsets in months based on start date
  $offsets = [];
  if ($plan === 'one') {
    $offsets = [0];
  } elseif ($plan === 'two') {
    $offsets = [0, 6];
  } elseif ($plan === 'three') {
    $offsets = [0, 4, 8];
  } elseif ($plan === 'four') {
    $offsets = [0, 3, 6, 9];
  } else { // monthly
    $offsets = [];
    for ($i=0; $i<$months; $i++) $offsets[] = $i;
  }

  $n = count($offsets);
  if ($n <= 0) throw new RuntimeException('Bad schedule');

  $base = floor(($totalAmount / $n) * 100) / 100;
  $sumBase = $base * ($n - 1);
  $last = round($totalAmount - $sumBase, 2);

  $stmt = $pdo->prepare("INSERT INTO contract_schedules (contract_id, seq, due_date, amount) VALUES (?,?,?,?)");
  for ($i=1; $i <= $n; $i++) {
    $due = add_months_keep_day($dtStart, $offsets[$i-1]);
    $amount = ($i === $n) ? $last : $base;
    $stmt->execute([$contractId, $i, $due->format('Y-m-d'), $amount]);
  }

  return ['installments'=>$n,'base'=>$base,'last'=>$last];
}

$body=json_decode(file_get_contents('php://input'), true);

$tenantName=safe_str($body['tenantName']??'');
$tenantEmail=strtolower(safe_str($body['tenantEmail']??''));
$tenantPhone=safe_str($body['tenantPhone']??'');
$tenantIdentity=safe_str($body['tenantIdentity']??'');
$propertyId=safe_int($body['propertyId']??0);
$unitId=safe_int($body['unitId']??0);
$contractNumber=safe_str($body['contractNumber']??'');
$startDate=safe_str($body['startDate']??'');
$months=safe_int($body['months']??0);
$dueDay=safe_int($body['dueDay']??0); // legacy (ignored)
$plan=safe_str($body['paymentPlan']??'monthly');
$totalAmount=(float)($body['totalAmount']??0);

if($tenantName===''||$tenantEmail===''||$propertyId<=0||$unitId<=0||$contractNumber===''||$startDate===''||$months<=0||$totalAmount<=0){
  json_error('البيانات غير مكتملة',422);
}

$pdo=db();
$dueDay = ($dueDay>=1 && $dueDay<=28) ? $dueDay : (int)(new DateTime($startDate))->format('j');
$st=$pdo->prepare("SELECT id FROM properties WHERE id=? AND owner_id=? LIMIT 1");
$st->execute([$propertyId,$ownerId]);
if(!$st->fetch()) json_error('العقار غير موجود',404);

$st=$pdo->prepare("SELECT u.id FROM units u JOIN properties p ON p.id=u.property_id WHERE u.id=? AND p.id=? AND p.owner_id=? LIMIT 1");
$st->execute([$unitId,$propertyId,$ownerId]);
if(!$st->fetch()) json_error('الوحدة غير موجودة',404);

$pdo->beginTransaction();
try{
  $st=$pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
  $st->execute([$tenantEmail]);
  $row=$st->fetch();
  $tenantId=$row?(int)$row['id']:0;

  $plainPw=null;
  if($tenantId<=0){
    $plainPw=random_password(10);
    $hash=password_hash_str($plainPw);
    $st=$pdo->prepare("INSERT INTO users (role,full_name,email,phone,password_hash) VALUES ('tenant',?,?,?,?)");
    $st->execute([$tenantName,$tenantEmail,$tenantPhone!==''?$tenantPhone:null,$hash]);
    $tenantId=(int)$pdo->lastInsertId();
  }

    // Insert contract (try with tenant_identity if the column exists)
  $contractId = 0;
  try{
    $st=$pdo->prepare("INSERT INTO contracts (owner_id,tenant_id,property_id,unit_id,contract_number,start_date,months,due_day,payment_plan,total_amount,tenant_identity)
                       VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $st->execute([$ownerId,$tenantId,$propertyId,$unitId,$contractNumber,$startDate,$months,$dueDay,$plan,$totalAmount, ($tenantIdentity!==''?$tenantIdentity:null)]);
    $contractId=(int)$pdo->lastInsertId();
  }catch(Throwable $e){
    $st=$pdo->prepare("INSERT INTO contracts (owner_id,tenant_id,property_id,unit_id,contract_number,start_date,months,due_day,payment_plan,total_amount)
                       VALUES (?,?,?,?,?,?,?,?,?,?)");
    $st->execute([$ownerId,$tenantId,$propertyId,$unitId,$contractNumber,$startDate,$months,$dueDay,$plan,$totalAmount]);
    $contractId=(int)$pdo->lastInsertId();
  }

  $st=$pdo->prepare("UPDATE units SET status='rented' WHERE id=?");
  $st->execute([$unitId]);

  $meta=build_schedule($pdo,$contractId,$startDate,$months,$plan,$totalAmount);

  $pdo->commit();

  $mailSent=true;
  if($plainPw!==null) $mailSent=send_tenant_welcome_email($tenantEmail,$tenantName,$plainPw);

  json_ok(['contractId'=>$contractId,'tenantId'=>$tenantId,'tenantCreated'=>($plainPw!==null),'mailSent'=>$mailSent,'scheduleMeta'=>$meta]);
}catch(Throwable $e){
  $pdo->rollBack();
  json_error('فشل إنشاء العقد',500,['error'=>$e->getMessage()]);
}
