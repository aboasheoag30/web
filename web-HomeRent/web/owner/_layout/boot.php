<?php
// owner/_layout/boot.php
declare(strict_types=1);

function page_title(string $t): string {
  return $t . " - ايجار ويب";
}

$active = $active ?? 'dashboard';
$title  = $title  ?? 'لوحة المالك';
?>
