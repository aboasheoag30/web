<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/_report_shared.php';
cors(); require_method('GET');

$me=require_auth();
$ownerId=require_owner_scope($me);
$propertyId = safe_int($_GET['propertyId'] ?? 0);

$pdo=db();
$r = fetch_owner_contract_report($pdo, $ownerId, $propertyId);

function day_name_ar(string $ymd): string {
  $dt = new DateTime($ymd);
  $en = $dt->format('l');
  $map = [
    'Saturday'=>'السبت','Sunday'=>'الأحد','Monday'=>'الإثنين','Tuesday'=>'الثلاثاء',
    'Wednesday'=>'الأربعاء','Thursday'=>'الخميس','Friday'=>'الجمعة'
  ];
  return $map[$en] ?? $en;
}

$now = new DateTime();
$stamp = $now->format('Y-m-d H:i:s');
$day = day_name_ar($now->format('Y-m-d'));
$source = APP_NAME;

$filename = 'ijarweb-report-' . $now->format('Ymd-His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');

echo "<html dir='rtl'><head><meta charset='utf-8'></head><body>";
echo "<h2>تقرير - ".$source."</h2>";
echo "<div>المصدر: ".$source." | التاريخ/الوقت: ".$stamp." | اليوم: ".$day."</div><hr/>";

echo "<h3>الإجماليات</h3>";
echo "<table border='1' cellpadding='6' cellspacing='0'>";
$tot = [
  'عدد العقود'=>$r['totals']['contracts'],
  'عقود متأخرة'=>$r['totals']['contractsWithOverdue'],
  'عقود خلال 30 يوم'=>$r['totals']['contractsWithDue30'],
  'عقود مستقبلية'=>$r['totals']['contractsWithFuture'],
  'إجمالي المتأخرات'=>number_format((float)$r['totals']['overdueAmount'],2),
  'إجمالي خلال 30 يوم'=>number_format((float)$r['totals']['due30Amount'],2),
  'إجمالي المستقبلية'=>number_format((float)$r['totals']['futureAmount'],2),
  'إجمالي المسدد'=>number_format((float)$r['totals']['paidAmount'],2),
  'إجمالي المتبقي'=>number_format((float)$r['totals']['remainingAmount'],2),
];
foreach($tot as $k=>$v){ echo "<tr><td><b>".$k."</b></td><td>".$v."</td></tr>"; }
echo "</table>";

echo "<h3>ملخص لكل عقار</h3>";
echo "<table border='1' cellpadding='6' cellspacing='0'><tr>
<th>العقار</th><th>عدد العقود</th><th>عقود متأخرة</th><th>عقود خلال 30</th><th>عقود مستقبلية</th>
<th>متأخرات</th><th>خلال 30</th><th>مستقبلية</th><th>مسدد</th><th>متبقي</th></tr>";
foreach($r['perProperty'] as $p){
  echo "<tr>";
  echo "<td>".$p['propertyName']."</td><td>".$p['contracts']."</td><td>".$p['contractsWithOverdue']."</td><td>".$p['contractsWithDue30']."</td><td>".$p['contractsWithFuture']."</td>";
  echo "<td>".number_format((float)$p['overdueAmount'],2)."</td><td>".number_format((float)$p['due30Amount'],2)."</td><td>".number_format((float)$p['futureAmount'],2)."</td>";
  echo "<td>".number_format((float)$p['paidAmount'],2)."</td><td>".number_format((float)$p['remainingAmount'],2)."</td>";
  echo "</tr>";
}
echo "</table>";

echo "<h3>قائمة العقود</h3>";
echo "<table border='1' cellpadding='6' cellspacing='0'><tr>
<th>العقار</th><th>الوحدة</th><th>رقم العقد</th><th>المستأجر</th><th>نهاية العقد</th><th>أيام للنهاية</th>
<th>مسدد</th><th>متبقي</th><th>متأخر</th><th>≤30</th><th>>30</th></tr>";
foreach($r['contracts'] as $c){
  echo "<tr>";
  echo "<td>".$c['propertyName']."</td><td>".$c['unitType']." ".$c['unitName']."</td><td>".$c['contractNumber']."</td><td>".$c['tenantName']."</td>";
  echo "<td>".$c['endDate']."</td><td>".$c['daysToEnd']."</td>";
  echo "<td>".number_format((float)$c['paidAmount'],2)."</td><td>".number_format((float)$c['remainingAmount'],2)."</td>";
  echo "<td>".number_format((float)$c['overdueAmount'],2)."</td><td>".number_format((float)$c['due30Amount'],2)."</td><td>".number_format((float)$c['futureAmount'],2)."</td>";
  echo "</tr>";
}
echo "</table>";
echo "</body></html>";
