<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth(); $ownerId=require_owner_scope($me);
$pdo=db();

$q1=$pdo->prepare("SELECT COUNT(*) c FROM units u JOIN properties p ON p.id=u.property_id WHERE p.owner_id=? AND u.status='rented'");
$q1->execute([$ownerId]); $rented=(int)$q1->fetch()['c'];

$q2=$pdo->prepare("SELECT COUNT(DISTINCT tenant_id) c FROM contracts WHERE owner_id=?");
$q2->execute([$ownerId]); $tenants=(int)$q2->fetch()['c'];

$q3=$pdo->prepare("SELECT COUNT(*) c FROM payment_requests pr JOIN contracts c ON c.id=pr.contract_id WHERE c.owner_id=? AND pr.status='pending'");
$q3->execute([$ownerId]); $pending=(int)$q3->fetch()['c'];

$q4=$pdo->prepare("SELECT IFNULL(SUM(p.amount),0) s FROM payments p JOIN contracts c ON c.id=p.contract_id WHERE c.owner_id=?");
$q4->execute([$ownerId]); $paid=(float)$q4->fetch()['s'];

$q5=$pdo->prepare("SELECT COUNT(*) c FROM properties WHERE owner_id=?");
$q5->execute([$ownerId]); $props=(int)$q5->fetch()['c'];

$q6=$pdo->prepare("SELECT COUNT(*) c FROM users WHERE role='staff' AND owner_id=?");
$q6->execute([$ownerId]); $staff=(int)$q6->fetch()['c'];

json_ok(['stats'=>[
  'properties'=>$props,'rentedUnits'=>$rented,'tenants'=>$tenants,'staffUsers'=>$staff,
  'pendingPaymentRequests'=>$pending,'totalPaid'=>$paid
]]);
