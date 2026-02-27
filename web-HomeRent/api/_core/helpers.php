<?php
declare(strict_types=1);

// ===== JSON Error Handling (global) =====
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

set_exception_handler(function($e){
  try{
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
  }catch(Throwable $t){}
  echo json_encode(['ok'=>false,'message'=>'Server error','detail'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
  exit;
});

set_error_handler(function($severity, $message, $file, $line){
  if(!(error_reporting() & $severity)) return false;
  try{
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
  }catch(Throwable $t){}
  echo json_encode(['ok'=>false,'message'=>'PHP error','detail'=>"$message @ $file:$line"], JSON_UNESCAPED_UNICODE);
  exit;
});

register_shutdown_function(function(){
  $err = error_get_last();
  if($err && in_array($err['type'], [E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR], true)){
    try{
      http_response_code(500);
      header('Content-Type: application/json; charset=utf-8');
    }catch(Throwable $t){}
    echo json_encode(['ok'=>false,'message'=>'Fatal error','detail'=>$err['message'].' @ '.$err['file'].':'.$err['line']], JSON_UNESCAPED_UNICODE);
    exit;
  }
});


function cors(): void {
  if (!headers_sent()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
  }
  if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }
}

function json_ok(array $data = []): void {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array_merge(['ok'=>true], $data), JSON_UNESCAPED_UNICODE);
  exit;
}
function json_error(string $message, int $status=400, array $extra=[]): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array_merge(['ok'=>false,'message'=>$message], $extra), JSON_UNESCAPED_UNICODE);
  exit;
}
function require_method(string $method): void {
  $m = $_SERVER['REQUEST_METHOD'] ?? 'GET';
  if ($m !== $method) json_error('Method not allowed', 405);
}
function safe_int($v, int $default=0): int {
  if ($v === null) return $default;
  if (is_numeric($v)) return (int)$v;
  return $default;
}
function safe_str($v, string $default=''): string {
  if ($v === null) return $default;
  return trim((string)$v);
}
function random_password(int $len=10): string {
  $chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
  $out='';
  for($i=0;$i<$len;$i++) $out.=$chars[random_int(0, strlen($chars)-1)];
  return $out;
}
function password_hash_str(string $pw): string { return password_hash($pw, PASSWORD_BCRYPT); }
