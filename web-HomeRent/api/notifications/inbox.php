<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth();
$uid=(int)$me['uid']; $role=(string)$me['role'];
$ownerScopeId=0;
if($role==='owner') $ownerScopeId=$uid;
if($role==='staff') $ownerScopeId=(int)($me['owner_id']??0);

$pdo=db();
$st=$pdo->prepare("SELECT id,title,body,created_at FROM notifications
                   WHERE (target_user_id=?)
                      OR (target_user_id IS NULL AND target_role=? AND owner_scope_id IS NULL)
                      OR (target_user_id IS NULL AND target_role=? AND owner_scope_id=?)
                   ORDER BY id DESC LIMIT 50");
$st->execute([$uid,$role,$role,$ownerScopeId]);
json_ok(['items'=>$st->fetchAll()]);
