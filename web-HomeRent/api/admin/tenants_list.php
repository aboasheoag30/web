<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('GET');

$me = require_auth();
$ownerId = require_owner_scope($me);

$q = trim((string)($_GET['q'] ?? ''));

$pdo = db();

$sql = "
SELECT t.id, t.full_name, t.email, t.phone,
  COUNT(c.id) AS contracts_count,
  MAX(c.start_date) AS last_contract_start
FROM contracts c
JOIN users t ON t.id=c.tenant_id
WHERE c.owner_id=?
";
$params = [$ownerId];

if($q !== ''){
  $like="%$q%";
  $sql .= " AND (t.full_name LIKE ? OR t.email LIKE ? OR t.phone LIKE ?)";
  array_push($params, $like,$like,$like);
}

$sql .= " GROUP BY t.id, t.full_name, t.email, t.phone
          ORDER BY t.id DESC LIMIT 300";
$st=$pdo->prepare($sql);
$st->execute($params);
$items=$st->fetchAll();

json_ok(['items'=>$items,'count'=>count($items)]);
