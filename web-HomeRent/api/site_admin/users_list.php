<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth();
if(!in_array(($me['role']??''), ['site_admin','site_admin_staff'], true)) json_error('Forbidden',403);
$role=safe_str($_GET['role']??'');
$allowed=['owner','staff','tenant','site_admin'];
$pdo=db();
if($role!=='' && in_array($role,$allowed,true)){
  $st=$pdo->prepare("SELECT id,role,owner_id,full_name,email,phone,status,created_at FROM users WHERE role=? ORDER BY id DESC LIMIT 200");
  $st->execute([$role]);
  json_ok(['items'=>$st->fetchAll()]);
}
$st=$pdo->query("SELECT id,role,owner_id,full_name,email,phone,status,created_at FROM users ORDER BY id DESC LIMIT 200");
json_ok(['items'=>$st->fetchAll()]);
