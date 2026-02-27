<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('GET');

$me = require_auth();
$ownerId = require_owner_scope($me);

$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));

$pdo = db();

$sql = "
SELECT c.id, c.contract_number, c.start_date, c.months, c.payment_plan, c.total_amount, c.status,
  DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) AS end_date,
  DATEDIFF(DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY), CURDATE()) AS days_to_end,
  t.full_name AS tenant_name, t.phone AS tenant_phone,
  p.name AS property_name, u.unit_type, u.name AS unit_name
FROM contracts c
JOIN users t ON t.id=c.tenant_id
JOIN properties p ON p.id=c.property_id
JOIN units u ON u.id=c.unit_id
WHERE c.owner_id=?
";
$params = [$ownerId];

if($status !== ''){
  $sql .= " AND c.status = ? ";
  $params[] = $status;
}

if($q !== ''){
  $like = "%$q%";
  $sql .= " AND (
    c.contract_number LIKE ?
    OR t.full_name LIKE ?
    OR t.phone LIKE ?
    OR p.name LIKE ?
    OR u.unit_type LIKE ?
    OR u.name LIKE ?
  )";
  array_push($params, $like,$like,$like,$like,$like,$like);
}

$sql .= " ORDER BY c.id DESC LIMIT 300";
$st = $pdo->prepare($sql);
$st->execute($params);
$items = $st->fetchAll();

json_ok(['items'=>$items, 'count'=>count($items)]);
