<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function send_mail_simple(string $to, string $subject, string $html): bool {
  $headers=[];
  $headers[]='MIME-Version: 1.0';
  $headers[]='Content-type: text/html; charset=utf-8';
  $headers[]='From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>';
  return mail($to, "=?UTF-8?B?".base64_encode($subject)."?=", $html, implode("\r\n",$headers));
}
function send_tenant_welcome_email(string $email, string $name, string $plainPassword): bool {
  $subject='بيانات الدخول - '.APP_NAME;
  $loginUrl=BASE_URL.'/web/tenant/login.html';
  $html="<div dir='rtl' style='font-family:Tahoma,Arial;line-height:1.8'>
    <h2>مرحباً {$name}</h2>
    <p>تم إنشاء حسابك في <b>".APP_NAME."</b>.</p>
    <p><b>البريد:</b> {$email}<br/><b>كلمة المرور:</b> {$plainPassword}</p>
    <p>رابط الدخول: <a href='{$loginUrl}'>{$loginUrl}</a></p>
  </div>";
  return send_mail_simple($email,$subject,$html);
}
function send_owner_new_request_email(string $ownerEmail, string $ownerName, string $tenantName, string $contractNumber): bool {
  $subject='طلب اعتماد سداد جديد - '.APP_NAME;
  $panelUrl=BASE_URL.'/web/owner/panel.html';
  $html="<div dir='rtl' style='font-family:Tahoma,Arial;line-height:1.8'>
    <h3>طلب اعتماد سداد جديد</h3>
    <p>وصل طلب اعتماد سداد من المستأجر: <b>{$tenantName}</b></p>
    <p>رقم العقد: <b>{$contractNumber}</b></p>
    <p>للمراجعة: <a href='{$panelUrl}'>{$panelUrl}</a></p>
  </div>";
  return send_mail_simple($ownerEmail,$subject,$html);
}
function send_notification_email(string $to, string $title, string $body): bool {
  $subject=$title.' - '.APP_NAME;
  $html="<div dir='rtl' style='font-family:Tahoma,Arial;line-height:1.8'>
    <h3>".htmlspecialchars($title)."</h3>
    <p>".nl2br(htmlspecialchars($body))."</p>
  </div>";
  return send_mail_simple($to,$subject,$html);
}
