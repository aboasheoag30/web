<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
cors(); require_method('POST');

$me=require_auth();
$ownerId=require_owner_scope($me);

$data=read_json();
$staffId=(int)($data['staffId'] ?? 0);
$email=strtolower(trim((string)($data['email'] ?? '')));
$full=trim((string)($data['fullName'] ?? ''));

if($staffId<=0) json_error('staffId required',400);
if($email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('Email invalid',400);

$pdo=db();
$st=$pdo->prepare("SELECT id, role, owner_id FROM users WHERE id=? LIMIT 1");
$st->execute([$staffId]);
$u=$st->fetch();
if(!$u) json_error('Not found',404);
if(($u['role']??'')!=='staff') json_error('Not staff user',400);
if((int)($u['owner_id']??0)!==$ownerId) json_error('Forbidden',403);

$st=$pdo->prepare("SELECT id FROM users WHERE email=? AND id<>? LIMIT 1");
$st->execute([$email,$staffId]);
if($st->fetch()) json_error('البريد مستخدم مسبقاً',400);

$st=$pdo->prepare("UPDATE users SET email=?, full_name=COALESCE(NULLIF(?,''), full_name) WHERE id=?");
$st->execute([$email,$full,$staffId]);

json_ok(['message'=>'Updated']);
