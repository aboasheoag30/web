<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth();
if(!in_array(($me['role']??''), ['site_admin','site_admin_staff'], true)) json_error('Forbidden',403);
$pdo=db();
$props=(int)$pdo->query("SELECT COUNT(*) c FROM properties")->fetch()['c'];
$units=(int)$pdo->query("SELECT COUNT(*) c FROM units")->fetch()['c'];
$owners=(int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='owner'")->fetch()['c'];
$staff=(int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='staff'")->fetch()['c'];
$tenants=(int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='tenant'")->fetch()['c'];
json_ok(['stats'=>['properties'=>$props,'units'=>$units,'owners'=>$owners,'staffUsers'=>$staff,'tenants'=>$tenants,'totalUsers'=>$owners+$staff+$tenants]]);
