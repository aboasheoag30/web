<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth(); $ownerId=require_owner_scope($me);
$propertyId=safe_int($_GET['propertyId']??0);
$pdo=db();

$params=[$ownerId];
$whereProp="";
if($propertyId>0){ $whereProp=" AND c.property_id=? "; $params[]=$propertyId; }

$st=$pdo->prepare("SELECT IFNULL(SUM(cs.amount),0) total_due
                   FROM contract_schedules cs JOIN contracts c ON c.id=cs.contract_id
                   WHERE c.owner_id=? {$whereProp}");
$st->execute($params);
$totalDue=(float)$st->fetch()['total_due'];

$st=$pdo->prepare("SELECT IFNULL(SUM(p.amount),0) total_paid
                   FROM payments p JOIN contracts c ON c.id=p.contract_id
                   WHERE c.owner_id=? {$whereProp}");
$st->execute($params);
$totalPaid=(float)$st->fetch()['total_paid'];

if($propertyId>0){
  $st=$pdo->prepare("SELECT COUNT(*) units_count FROM units WHERE property_id=?");
  $st->execute([$propertyId]);
}else{
  $st=$pdo->prepare("SELECT COUNT(*) units_count FROM units u JOIN properties pr ON pr.id=u.property_id WHERE pr.owner_id=?");
  $st->execute([$ownerId]);
}
$unitsCount=(int)$st->fetch()['units_count'];

$st=$pdo->prepare("SELECT COUNT(DISTINCT tenant_id) tenants_count FROM contracts c WHERE c.owner_id=? {$whereProp}");
$st->execute($params);
$tenantsCount=(int)$st->fetch()['tenants_count'];

json_ok(['propertyId'=>$propertyId>0?$propertyId:null,'summary'=>[
  'totalDue'=>$totalDue,'totalPaid'=>$totalPaid,'totalRemaining'=>max(0,$totalDue-$totalPaid),
  'unitsCount'=>$unitsCount,'tenantsCount'=>$tenantsCount
]]);
