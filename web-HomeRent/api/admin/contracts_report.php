<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/_report_shared.php';
cors(); require_method('GET');

$me=require_auth();
$ownerId=require_owner_scope($me);
$propertyId = safe_int($_GET['propertyId'] ?? 0);

$pdo=db();
$data = fetch_owner_contract_report($pdo, $ownerId, $propertyId);
json_ok($data);
