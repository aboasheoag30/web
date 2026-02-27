<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth();
if(($me['role']??'')!=='tenant') json_error('Forbidden',403);
$year=safe_int($_GET['year']??date('Y'), (int)date('Y'));
$pdo=db();
$st=$pdo->prepare("SELECT c.id AS contractId,c.contract_number,cs.id AS scheduleId,cs.seq,cs.due_date,cs.amount,cs.status
                   FROM contracts c JOIN contract_schedules cs ON cs.contract_id=c.id
                   WHERE c.tenant_id=? AND YEAR(cs.due_date)=?
                   ORDER BY cs.due_date ASC");
$st->execute([(int)$me['uid'], $year]);
json_ok(['items'=>$st->fetchAll()]);
