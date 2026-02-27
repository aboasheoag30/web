<?php
declare(strict_types=1);
/**
 * Cron job: Daily notifications for owners:
 * - Overdue installments (due_date < today and status='unpaid')
 * - Installments due within 30 days (today..today+30) status='unpaid'
 * - Contracts ending within 60 days (end_date <= today+60) status='active'
 *
 * Usage:
 *   php api/cron/daily_notifications.php
 * or via web (protected by CRON_TOKEN):
 *   https://yourdomain/.../api/cron/daily_notifications.php?token=YOURTOKEN
 */
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/config.php';
require_once __DIR__ . '/../_core/mailer.php';

cors();

const JOB_KEY = 'daily_owner_alerts';

function require_cron_token(): void {
  $token = $_GET['token'] ?? '';
  // Optional: set CRON_TOKEN in config.php if you want to protect HTTP access
  if(defined('CRON_TOKEN') && CRON_TOKEN !== ''){
    if($token !== CRON_TOKEN){
      http_response_code(403);
      echo "Forbidden";
      exit;
    }
  }
}

if (php_sapi_name() !== 'cli') {
  require_cron_token();
}

$pdo = db();
$today = (new DateTime('today'))->format('Y-m-d');
$in30 = (new DateTime('today'))->modify('+30 day')->format('Y-m-d');
$in60 = (new DateTime('today'))->modify('+60 day')->format('Y-m-d');

try{
  // prevent duplicates per day
  $st=$pdo->prepare("INSERT IGNORE INTO cron_runs (job_key, run_date) VALUES (?, ?)");
  $st->execute([JOB_KEY, $today]);
  if($st->rowCount() === 0){
    json_ok(['message'=>'Already ran today', 'date'=>$today]);
  }

  // list all active owners
  $owners = $pdo->query("SELECT id, full_name, email FROM users WHERE role='owner' AND status='active'")->fetchAll();

  $insertNotif = $pdo->prepare("INSERT INTO notifications (target_role, target_user_id, owner_scope_id, title, body, send_email)
                                VALUES ('owner', NULL, ?, ?, ?, ?)");

  foreach($owners as $o){
    $ownerId = (int)$o['id'];

    // Overdue unpaid schedules
    $st=$pdo->prepare("SELECT COUNT(*) c, IFNULL(SUM(cs.amount),0) s
                       FROM contract_schedules cs
                       JOIN contracts c ON c.id=cs.contract_id
                       WHERE c.owner_id=? AND c.status='active'
                         AND cs.status='unpaid' AND cs.due_date < ?");
    $st->execute([$ownerId, $today]);
    $over = $st->fetch();
    $overCount = (int)$over['c'];
    $overSum = (float)$over['s'];

    // Due within 30 days
    $st=$pdo->prepare("SELECT COUNT(*) c, IFNULL(SUM(cs.amount),0) s
                       FROM contract_schedules cs
                       JOIN contracts c ON c.id=cs.contract_id
                       WHERE c.owner_id=? AND c.status='active'
                         AND cs.status='unpaid' AND cs.due_date >= ? AND cs.due_date <= ?");
    $st->execute([$ownerId, $today, $in30]);
    $due = $st->fetch();
    $dueCount = (int)$due['c'];
    $dueSum = (float)$due['s'];

    // Contracts ending within 60 days
    // end_date = start_date + months - 1 day
    $st=$pdo->prepare("SELECT COUNT(*) c
                       FROM contracts c
                       WHERE c.owner_id=? AND c.status='active'
                         AND DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) <= ?
                         AND DATE_SUB(DATE_ADD(c.start_date, INTERVAL c.months MONTH), INTERVAL 1 DAY) >= ?");
    $st->execute([$ownerId, $in60, $today]);
    $endCount = (int)$st->fetch()['c'];

    // If nothing, skip creating noise
    if($overCount===0 && $dueCount===0 && $endCount===0) continue;

    $title = "تنبيهات اليوم - الاستحقاقات والعقود";
    $body = "ملخص تنبيهات اليوم بتاريخ: {$today}\n"
          . "• المتأخرات: {$overCount} (الإجمالي: " . number_format($overSum,2) . " ريال)\n"
          . "• تستحق خلال 30 يوم: {$dueCount} (الإجمالي: " . number_format($dueSum,2) . " ريال)\n"
          . "• عقود تنتهي خلال 60 يوم: {$endCount}\n\n"
          . "للمتابعة ادخل لوحة المالك.";

    $sendEmail = 1; // send emails for owners
    $insertNotif->execute([$ownerId, $title, $body, $sendEmail]);

    if(!empty($o['email'])){
      send_notification_email((string)$o['email'], $title, $body);
    }
  }

  json_ok(['message'=>'Done', 'date'=>$today, 'in30'=>$in30, 'in60'=>$in60]);
}catch(Throwable $e){
  json_error('Cron failed',500,['error'=>$e->getMessage()]);
}
