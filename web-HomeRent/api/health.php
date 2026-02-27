<?php
declare(strict_types=1);
require_once __DIR__ . '/_core/helpers.php';
require_once __DIR__ . '/_core/db.php';

cors(); require_method('GET');

try{
  $pdo = db();
  $pdo->query("SELECT 1")->fetch();
  json_ok(['db'=>true,'time'=>date('Y-m-d H:i:s')]);
}catch(Throwable $e){
  json_error('DB connection failed', 500, ['detail'=>$e->getMessage()]);
}
