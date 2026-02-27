<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/mailer.php';
cors(); require_method('POST');
$me=require_auth();
if(!in_array(($me['role']??''), ['site_admin','site_admin_staff'], true)) json_error('Forbidden',403);

$body=json_decode(file_get_contents('php://input'), true);
$targetRole=safe_str($body['targetRole']??'');
$title=safe_str($body['title']??'');
$msg=safe_str($body['body']??'');
$targetUserId=safe_int($body['targetUserId']??0);
$ownerScopeId=safe_int($body['ownerScopeId']??0);
$sendEmail=(int)($body['sendEmail']??0);

$allowedRoles=['owner','staff','tenant','site_admin'];
if(!in_array($targetRole,$allowedRoles,true) || $title==='' || $msg==='') json_error('بيانات غير مكتملة',422);

$pdo=db();
$st=$pdo->prepare("INSERT INTO notifications (target_role,target_user_id,owner_scope_id,title,body,send_email) VALUES (?,?,?,?,?,?)");
$st->execute([$targetRole,$targetUserId>0?$targetUserId:null,$ownerScopeId>0?$ownerScopeId:null,$title,$msg,$sendEmail?1:0]);
$notifId=(int)$pdo->lastInsertId();

if($sendEmail){
  if($targetUserId>0){
    $st=$pdo->prepare("SELECT email FROM users WHERE id=? AND status='active' LIMIT 1");
    $st->execute([$targetUserId]);
    $u=$st->fetch();
    if($u && $u['email']) send_notification_email($u['email'],$title,$msg);
  }else{
    if($ownerScopeId>0 && ($targetRole==='staff' || $targetRole==='tenant')){
      if($targetRole==='staff'){
        $st=$pdo->prepare("SELECT email FROM users WHERE role='staff' AND owner_id=? AND status='active'");
        $st->execute([$ownerScopeId]);
        while($u=$st->fetch()){ if($u['email']) send_notification_email($u['email'],$title,$msg); }
      }else{
        $st=$pdo->prepare("SELECT DISTINCT u.email FROM users u JOIN contracts c ON c.tenant_id=u.id
                           WHERE u.role='tenant' AND c.owner_id=? AND u.status='active'");
        $st->execute([$ownerScopeId]);
        while($u=$st->fetch()){ if($u['email']) send_notification_email($u['email'],$title,$msg); }
      }
    }else{
      $st=$pdo->prepare("SELECT email FROM users WHERE role=? AND status='active'");
      $st->execute([$targetRole]);
      while($u=$st->fetch()){ if($u['email']) send_notification_email($u['email'],$title,$msg); }
    }
  }
}

json_ok(['notificationId'=>$notifId]);
