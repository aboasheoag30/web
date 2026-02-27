<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

/**
 * auth.php
 * - JWT sign/verify
 * - قراءة التوكن من Authorization / getallheaders / REDIRECT_HTTP_AUTHORIZATION / cookie
 * - مناسب لـ QNAP (أحيانًا لا يمرر Authorization)
 */

function base64url_encode(string $data): string {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
  $pad = strlen($data) % 4;
  if ($pad) $data .= str_repeat('=', 4 - $pad);
  return base64_decode(strtr($data, '-_', '+/')) ?: '';
}

function jwt_sign(array $payload): string {
  $header = ['alg' => 'HS256', 'typ' => 'JWT'];

  // حماية: لا تسمح للعميل يمرر exp/iat/iss من نفسه
  unset($payload['iss'], $payload['iat'], $payload['exp']);

  $payload['iss'] = JWT_ISSUER;
  $payload['iat'] = time();
  $payload['exp'] = time() + JWT_TTL_SECONDS;

  $h = base64url_encode(json_encode($header, JSON_UNESCAPED_UNICODE));
  $p = base64url_encode(json_encode($payload, JSON_UNESCAPED_UNICODE));
  $sig = base64url_encode(hash_hmac('sha256', "$h.$p", JWT_SECRET, true));

  return "$h.$p.$sig";
}

function jwt_verify(string $token): ?array {
  $parts = explode('.', $token);
  if (count($parts) !== 3) return null;

  [$h, $p, $s] = $parts;

  $sig = base64url_encode(hash_hmac('sha256', "$h.$p", JWT_SECRET, true));
  if (!hash_equals($sig, $s)) return null;

  $payload = json_decode(base64url_decode($p), true);
  if (!is_array($payload)) return null;

  if (($payload['exp'] ?? 0) < time()) return null;

  // iss check (اختياري لكنه مفيد)
  if (isset($payload['iss']) && (string)$payload['iss'] !== (string)JWT_ISSUER) return null;

  return $payload;
}

/**
 * استخراج Bearer Token من:
 * 1) HTTP_AUTHORIZATION
 * 2) REDIRECT_HTTP_AUTHORIZATION
 * 3) getallheaders()
 * 4) Cookie (ijarweb_token)
 */
function get_bearer_token(): string {
  $auth = (string)($_SERVER['HTTP_AUTHORIZATION'] ?? '');

  if ($auth === '') {
    $auth = (string)($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
  }

  if ($auth === '' && function_exists('getallheaders')) {
    $headers = getallheaders();
    foreach ($headers as $k => $v) {
      if (strtolower((string)$k) === 'authorization') {
        $auth = (string)$v;
        break;
      }
    }
  }

  if ($auth !== '' && preg_match('/Bearer\s+(.+)/i', $auth, $m)) {
    return trim((string)$m[1]);
  }

  // ✅ fallback cookie (مهم في QNAP/بعض البروكسي)
  if (!empty($_COOKIE['ijarweb_token'])) {
    return trim((string)$_COOKIE['ijarweb_token']);
  }

  return '';
}

function require_auth(): array {
  $token = get_bearer_token();
  if ($token === '') json_error('Unauthorized', 401);

  $payload = jwt_verify($token);
  if (!$payload) json_error('Unauthorized', 401);

  return $payload;
}

function require_owner_scope(array $me): int {
  $role = (string)($me['role'] ?? '');

  if ($role === 'owner') return (int)($me['uid'] ?? 0);

  if ($role === 'staff') {
    $oid = (int)($me['owner_id'] ?? 0);
    if ($oid <= 0) json_error('Forbidden', 403);
    return $oid;
  }

  json_error('Forbidden', 403);
  return 0;
}