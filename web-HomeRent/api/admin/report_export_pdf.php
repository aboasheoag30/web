<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/_report_shared.php';
require_once __DIR__ . '/../_core/fpdf.php';
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

$pdf = new FPDF('P','mm','A4');
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
$pdf->SetFont('helvetica','B',14);
$pdf->Cell(0,8,'Report - '.$source,0,1,'C');
$pdf->SetFont('helvetica','',10);
$pdf->Cell(0,6,'Source: '.$source.' | DateTime: '.$stamp.' | Day: '.$day,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Totals',0,1,'L');
$pdf->SetFont('helvetica','',10);

$rows = [
 ['Contracts', (string)$r['totals']['contracts']],
 ['Contracts Overdue', (string)$r['totals']['contractsWithOverdue']],
 ['Contracts Due <=30', (string)$r['totals']['contractsWithDue30']],
 ['Contracts Future >30', (string)$r['totals']['contractsWithFuture']],
 ['Overdue Amount', number_format((float)$r['totals']['overdueAmount'],2)],
 ['Due <=30 Amount', number_format((float)$r['totals']['due30Amount'],2)],
 ['Future Amount', number_format((float)$r['totals']['futureAmount'],2)],
 ['Paid Amount', number_format((float)$r['totals']['paidAmount'],2)],
 ['Remaining Amount', number_format((float)$r['totals']['remainingAmount'],2)],
];

$col1=95; $col2=85;
foreach($rows as $rr){
  $pdf->Cell($col1,7,$rr[0],1,0,'L');
  $pdf->Cell($col2,7,$rr[1],1,1,'C');
}
$pdf->Ln(3);

$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Per Property',0,1,'L');
$pdf->SetFont('helvetica','B',8);
$pdf->Cell(55,7,'Property',1,0,'L');
$pdf->Cell(14,7,'C',1,0,'C');
$pdf->Cell(14,7,'OD',1,0,'C');
$pdf->Cell(14,7,'<=30',1,0,'C');
$pdf->Cell(14,7,'>30',1,0,'C');
$pdf->Cell(23,7,'OD Amt',1,0,'C');
$pdf->Cell(23,7,'<=30 Amt',1,0,'C');
$pdf->Cell(23,7,'>30 Amt',1,1,'C');

$pdf->SetFont('helvetica','',8);
foreach($r['perProperty'] as $p){
  $pdf->Cell(55,7,(string)$p['propertyName'],1,0,'L');
  $pdf->Cell(14,7,(string)$p['contracts'],1,0,'C');
  $pdf->Cell(14,7,(string)$p['contractsWithOverdue'],1,0,'C');
  $pdf->Cell(14,7,(string)$p['contractsWithDue30'],1,0,'C');
  $pdf->Cell(14,7,(string)$p['contractsWithFuture'],1,0,'C');
  $pdf->Cell(23,7,number_format((float)$p['overdueAmount'],2),1,0,'C');
  $pdf->Cell(23,7,number_format((float)$p['due30Amount'],2),1,0,'C');
  $pdf->Cell(23,7,number_format((float)$p['futureAmount'],2),1,1,'C');
}

$pdf->AddPage();
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Contracts',0,1,'L');
$pdf->SetFont('helvetica','B',7);
$pdf->Cell(42,7,'Property',1,0,'L');
$pdf->Cell(28,7,'Unit',1,0,'L');
$pdf->Cell(20,7,'No',1,0,'C');
$pdf->Cell(30,7,'Tenant',1,0,'L');
$pdf->Cell(18,7,'End',1,0,'C');
$pdf->Cell(10,7,'Days',1,0,'C');
$pdf->Cell(16,7,'Paid',1,0,'C');
$pdf->Cell(16,7,'Remain',1,0,'C');
$pdf->Cell(16,7,'Overdue',1,1,'C');

$pdf->SetFont('helvetica','',7);
foreach($r['contracts'] as $c){
  $pdf->Cell(42,7,(string)$c['propertyName'],1,0,'L');
  $pdf->Cell(28,7,(string)$c['unitType'].' '.(string)$c['unitName'],1,0,'L');
  $pdf->Cell(20,7,(string)$c['contractNumber'],1,0,'C');
  $pdf->Cell(30,7,(string)$c['tenantName'],1,0,'L');
  $pdf->Cell(18,7,(string)$c['endDate'],1,0,'C');
  $pdf->Cell(10,7,(string)$c['daysToEnd'],1,0,'C');
  $pdf->Cell(16,7,number_format((float)$c['paidAmount'],2),1,0,'C');
  $pdf->Cell(16,7,number_format((float)$c['remainingAmount'],2),1,0,'C');
  $pdf->Cell(16,7,number_format((float)$c['overdueAmount'],2),1,1,'C');
}

$filename = 'ijarweb-report-' . $now->format('Ymd-His') . '.pdf';
$pdf->Output('D', $filename);
