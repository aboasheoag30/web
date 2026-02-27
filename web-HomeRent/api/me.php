<?php
declare(strict_types=1);
require_once __DIR__ . '/_core/helpers.php';
require_once __DIR__ . '/_core/auth.php';
cors(); require_method('GET');
$me = require_auth();
json_ok(['me'=>$me]);
