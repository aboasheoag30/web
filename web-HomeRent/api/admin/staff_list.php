<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('GET');

$me=require_auth();
$ownerId=require_owner_scope($me);

$pdo=db();
$st=$pdo->prepare("SELECT id, full_name, email, phone, status, created_at FROM users WHERE role='staff' AND owner_id=? ORDER BY id DESC LIMIT 200");
$st->execute([$ownerId]);
$items=$st->fetchAll();
json_ok(['items'=>$items, 'count'=>count($items)]);
