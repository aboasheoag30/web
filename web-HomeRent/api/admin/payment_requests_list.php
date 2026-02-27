<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
cors(); require_method('GET');
$me=require_auth(); $ownerId=require_owner_scope($me);
$status=safe_str($_GET['status']??'pending');
if(!in_array($status,['pending','approved','rejected'],true)) $status='pending';
$pdo=db();
$st=$pdo->prepare("SELECT pr.id,pr.status,pr.notes,pr.uploaded_at,u.full_name AS tenant_name,u.email AS tenant_email,
                          c.contract_number,cs.seq,cs.due_date,cs.amount,a.kind,a.file_path
                   FROM payment_requests pr
                   JOIN contracts c ON c.id=pr.contract_id
                   JOIN users u ON u.id=pr.tenant_id
                   JOIN contract_schedules cs ON cs.id=pr.schedule_id
                   LEFT JOIN attachments a ON a.payment_request_id=pr.id
                   WHERE c.owner_id=? AND pr.status=?
                   ORDER BY pr.uploaded_at DESC");
$st->execute([$ownerId,$status]);
json_ok(['items'=>$st->fetchAll()]);
