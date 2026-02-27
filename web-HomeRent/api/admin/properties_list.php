<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth(); $ownerId=require_owner_scope($me);
$pdo=db();
$st=$pdo->prepare("SELECT id,name,city,district,created_at FROM properties WHERE owner_id=? ORDER BY id DESC");
$st->execute([$ownerId]);
json_ok(['items'=>$st->fetchAll()]);
