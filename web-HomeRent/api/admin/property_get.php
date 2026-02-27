<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('GET');

$me=require_auth();
$ownerId=require_owner_scope($me);
$propertyId = safe_int($_GET['id'] ?? 0);
if($propertyId<=0) json_error('id required',400);

$pdo=db();
$st=$pdo->prepare("SELECT id,name,city,district FROM properties WHERE id=? AND owner_id=? LIMIT 1");
$st->execute([$propertyId,$ownerId]);
$p=$st->fetch();
if(!$p) json_error('Not found',404);

$st=$pdo->prepare("SELECT id,unit_type,name,rent_amount,status FROM units WHERE property_id=? ORDER BY id DESC");
$st->execute([$propertyId]);
$units=$st->fetchAll();

$st=$pdo->prepare("
SELECT c.id, c.contract_number, c.start_date, c.months,
  DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) AS end_date,
  DATEDIFF(DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY), CURDATE()) AS days_to_end,
  t.full_name AS tenant_name, t.phone AS tenant_phone, t.email AS tenant_email,
  u.id AS unit_id, u.unit_type, u.name AS unit_name
FROM contracts c
JOIN users t ON t.id=c.tenant_id
JOIN units u ON u.id=c.unit_id
WHERE c.owner_id=? AND c.property_id=? AND c.status='active'
ORDER BY c.id DESC
");
$st->execute([$ownerId,$propertyId]);
$contracts=$st->fetchAll();

$st=$pdo->prepare("
SELECT
  c.id AS contract_id,
  IFNULL(SUM(cs.amount),0) AS total_amount,
  IFNULL(SUM(CASE WHEN cs.status='unpaid' AND cs.due_date < CURDATE() THEN cs.amount ELSE 0 END),0) AS overdue_amount,
  IFNULL(SUM(CASE WHEN cs.status='unpaid' AND cs.due_date >= CURDATE() AND cs.due_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN cs.amount ELSE 0 END),0) AS due30_amount,
  IFNULL(SUM(CASE WHEN cs.status='unpaid' AND cs.due_date > DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN cs.amount ELSE 0 END),0) AS future_amount,
  IFNULL(pay.paid_amount,0) AS paid_amount
FROM contracts c
JOIN contract_schedules cs ON cs.contract_id=c.id
LEFT JOIN (SELECT contract_id, IFNULL(SUM(amount),0) AS paid_amount FROM payments GROUP BY contract_id) pay ON pay.contract_id=c.id
WHERE c.owner_id=? AND c.property_id=? AND c.status='active'
GROUP BY c.id
");
$st->execute([$ownerId,$propertyId]);
$finRows=$st->fetchAll();
$finMap=[];
foreach($finRows as $r){ $finMap[(string)$r['contract_id']]=$r; }

json_ok(['property'=>$p,'units'=>$units,'contracts'=>$contracts,'finance'=>$finMap]);
