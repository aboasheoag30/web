<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';

function fetch_owner_contract_report(PDO $pdo, int $ownerId, int $propertyId=0): array {
  $todayDt = new DateTime('today');
  $today = $todayDt->format('Y-m-d');
  $in30 = (clone $todayDt)->modify('+30 day')->format('Y-m-d');

  $whereProp = "";
  $params = [$today, $today, $today, $in30, $in30, $ownerId];
  if($propertyId>0){ $whereProp=" AND c.property_id=? "; $params[]=$propertyId; }

  $sql = "
  SELECT
    c.id AS contract_id,
    c.contract_number,
    c.start_date,
    c.months,
    DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) AS end_date,
    DATEDIFF(DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY), ?) AS days_to_end,
    t.full_name AS tenant_name,
    p.id AS property_id,
    p.name AS property_name,
    u.unit_type,
    u.name AS unit_name,

    IFNULL(SUM(cs.amount),0) AS total_schedule_amount,
    IFNULL(SUM(CASE WHEN cs.status='unpaid' AND cs.due_date < ? THEN cs.amount ELSE 0 END),0) AS overdue_amount,
    IFNULL(SUM(CASE WHEN cs.status='unpaid' AND cs.due_date >= ? AND cs.due_date <= ? THEN cs.amount ELSE 0 END),0) AS due_30_amount,
    IFNULL(SUM(CASE WHEN cs.status='unpaid' AND cs.due_date > ? THEN cs.amount ELSE 0 END),0) AS future_amount,

    IFNULL(pay.paid_amount,0) AS paid_amount

  FROM contracts c
  JOIN users t ON t.id=c.tenant_id
  JOIN properties p ON p.id=c.property_id
  JOIN units u ON u.id=c.unit_id
  JOIN contract_schedules cs ON cs.contract_id=c.id
  LEFT JOIN (
    SELECT contract_id, IFNULL(SUM(amount),0) AS paid_amount
    FROM payments
    GROUP BY contract_id
  ) pay ON pay.contract_id=c.id

  WHERE c.owner_id=? AND c.status='active' {$whereProp}
  GROUP BY c.id
  ORDER BY p.id DESC, c.id DESC
  ";

  $st=$pdo->prepare($sql);
  $st->execute($params);
  $items=$st->fetchAll();

  $contracts=[];
  $totalsAll=[
    'contracts'=>0,
    'contractsWithOverdue'=>0,
    'contractsWithDue30'=>0,
    'contractsWithFuture'=>0,
    'overdueAmount'=>0.0,
    'due30Amount'=>0.0,
    'futureAmount'=>0.0,
    'paidAmount'=>0.0,
    'remainingAmount'=>0.0
  ];
  $perProperty=[];

  foreach($items as $r){
    $pid=(int)$r['property_id'];
    $over=(float)$r['overdue_amount'];
    $due30=(float)$r['due_30_amount'];
    $future=(float)$r['future_amount'];
    $paid=(float)$r['paid_amount'];
    $total=(float)$r['total_schedule_amount'];
    $remaining=max(0.0, $total - $paid);

    $contracts[]=[
      'contractId'=>(int)$r['contract_id'],
      'contractNumber'=>$r['contract_number'],
      'propertyId'=>$pid,
      'propertyName'=>$r['property_name'],
      'unitType'=>$r['unit_type'],
      'unitName'=>$r['unit_name'],
      'tenantName'=>$r['tenant_name'],
      'startDate'=>$r['start_date'],
      'endDate'=>$r['end_date'],
      'daysToEnd'=>(int)$r['days_to_end'],
      'paidAmount'=>$paid,
      'remainingAmount'=>$remaining,
      'overdueAmount'=>$over,
      'due30Amount'=>$due30,
      'futureAmount'=>$future
    ];

    $totalsAll['contracts'] += 1;
    if($over>0) $totalsAll['contractsWithOverdue'] += 1;
    if($due30>0) $totalsAll['contractsWithDue30'] += 1;
    if($future>0) $totalsAll['contractsWithFuture'] += 1;

    $totalsAll['overdueAmount'] += $over;
    $totalsAll['due30Amount'] += $due30;
    $totalsAll['futureAmount'] += $future;
    $totalsAll['paidAmount'] += $paid;
    $totalsAll['remainingAmount'] += $remaining;

    if(!isset($perProperty[$pid])){
      $perProperty[$pid]=[
        'propertyId'=>$pid,
        'propertyName'=>$r['property_name'],
        'contracts'=>0,
        'contractsWithOverdue'=>0,
        'contractsWithDue30'=>0,
        'contractsWithFuture'=>0,
        'overdueAmount'=>0.0,
        'due30Amount'=>0.0,
        'futureAmount'=>0.0,
        'paidAmount'=>0.0,
        'remainingAmount'=>0.0
      ];
    }
    $perProperty[$pid]['contracts'] += 1;
    if($over>0) $perProperty[$pid]['contractsWithOverdue'] += 1;
    if($due30>0) $perProperty[$pid]['contractsWithDue30'] += 1;
    if($future>0) $perProperty[$pid]['contractsWithFuture'] += 1;

    $perProperty[$pid]['overdueAmount'] += $over;
    $perProperty[$pid]['due30Amount'] += $due30;
    $perProperty[$pid]['futureAmount'] += $future;
    $perProperty[$pid]['paidAmount'] += $paid;
    $perProperty[$pid]['remainingAmount'] += $remaining;
  }

  return [
    'asOf'=>$today,
    'rangeDue30To'=>$in30,
    'propertyId'=>$propertyId>0?$propertyId:null,
    'totals'=>$totalsAll,
    'perProperty'=>array_values($perProperty),
    'contracts'=>$contracts
  ];
}
