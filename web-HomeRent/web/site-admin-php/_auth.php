<?php
declare(strict_types=1);

$API_CORE = realpath(__DIR__ . '/../../api/_core');
if (!$API_CORE) {
  http_response_code(500);
  echo "API core path not found. Expected: " . __DIR__ . "/../../api/_core";
  exit;
}

require_once $API_CORE . '/helpers.php';
require_once $API_CORE . '/db.php';
require_once $API_CORE . '/auth.php';

function must_be_site_admin_or_staff(): array {
  $me = require_auth(); // يقرأ من cookie ijarweb_token

  $role = (string)($me['role'] ?? '');
  if ($role !== 'site_admin' && $role !== 'site_admin_staff') {
    json_error('Forbidden', 403);
  }

  $pdo = db();
  $uid = (int)($me['uid'] ?? 0);
  if ($uid <= 0) json_error('Unauthorized', 401);

  $st = $pdo->prepare("SELECT id,email,role,status,is_permanent,access_until FROM users WHERE id=? LIMIT 1");
  $st->execute([$uid]);
  $u = $st->fetch();
  if (!$u) json_error('Unauthorized', 401);

  if (($u['status'] ?? '') !== 'active') {
    json_error('Unauthorized', 401, ['detail' => 'Account disabled']);
  }

  $isPerm = (int)($u['is_permanent'] ?? 1) === 1;
  $until  = (string)($u['access_until'] ?? '');
  if (!$isPerm && $until !== '') {
    if ($until < date('Y-m-d')) {
      json_error('Unauthorized', 401, ['detail' => 'Subscription expired']);
    }
  }

  return ['jwt' => $me, 'db' => $u];
}

function is_super_admin_email(string $email): bool {
  return strtolower(trim($email)) === strtolower('admin@Homerent.com');
}